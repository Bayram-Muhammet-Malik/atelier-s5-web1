<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use nrv\application\dto\CommandeDTO;
use nrv\application\exceptions\AccesRefuseException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceCommandeInterface;
use nrv\application\ports\spi\CommandeRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\application\ports\spi\UserRepositoryInterface;
use nrv\domain\entities\Billet;
use nrv\domain\entities\Commande;

class ServiceCommande implements ServiceCommandeInterface {
    private const QUANTITE_MAX = 10;
    private const NB_SOIREES_PASS = 3;
    private const REMISE_PASS = 0.03;

    private CommandeRepositoryInterface $commandeRepository;
    private SoireeRepositoryInterface $soireeRepository;
    private TarifRepositoryInterface $tarifRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        CommandeRepositoryInterface $commandeRepository,
        SoireeRepositoryInterface $soireeRepository,
        TarifRepositoryInterface $tarifRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->commandeRepository = $commandeRepository;
        $this->soireeRepository = $soireeRepository;
        $this->tarifRepository = $tarifRepository;
        $this->userRepository = $userRepository;
    }

    public function creerCommande(string $idUtilisateur, array $lignes): CommandeDTO {
        $utilisateur = $this->userRepository->findById($idUtilisateur);
        if ($utilisateur === null) {
            throw new DataNotFoundException("Utilisateur introuvable.");
        }

        if (count($lignes) === 0) {
            throw new ValidationException(['panier' => "Le panier est vide."]);
        }

        $tarifs = [];
        foreach ($this->tarifRepository->findAll() as $tarif) {
            $type = str_replace('é', 'e', strtolower($tarif->getLibelle()));
            $tarifs[$type] = $tarif;
        }

        $billets = [];
        $montantTotal = 0.0;
        $idCommande = Uuid::uuid4()->toString();

        foreach ($lignes as $ligne) {
            $tarifType = $ligne['tarifType'] ?? '';
            $quantite = $ligne['quantite'] ?? 0;
            $estPass = ($ligne['pass'] ?? false) === true;

            if (!is_int($quantite) || $quantite < 1 || $quantite > self::QUANTITE_MAX) {
                throw new ValidationException(['quantite' => "La quantité doit être comprise entre 1 et " . self::QUANTITE_MAX . "."]);
            }
            if (!isset($tarifs[$tarifType])) {
                throw new ValidationException(['tarifType' => "Le tarif doit être « normal » ou « reduit »."]);
            }
            $tarif = $tarifs[$tarifType];

            if ($estPass) {
                $idsSoirees = $ligne['soireeIds'] ?? [];
                if (!is_array($idsSoirees) || count(array_unique($idsSoirees)) !== self::NB_SOIREES_PASS) {
                    throw new ValidationException(['soireeIds' => "Un pass 3 jours doit contenir 3 soirées différentes."]);
                }
                $prix = round(self::NB_SOIREES_PASS * $tarif->getPrix() * (1 - self::REMISE_PASS), 2);
            } else {
                $idsSoirees = [$ligne['soireeId'] ?? ''];
                $prix = $tarif->getPrix();
            }

            $soirees = [];
            foreach ($idsSoirees as $idSoiree) {
                $soiree = $this->soireeRepository->getSoireeById((string) $idSoiree);
                if ($soiree === null) {
                    throw new ValidationException(['soireeId' => "La soirée demandée n'existe pas."]);
                }
                $soirees[] = $soiree;
            }

            for ($i = 0; $i < $quantite; $i++) {
                $billets[] = new Billet(
                    Uuid::uuid4()->toString(),
                    'NRV-' . strtoupper(bin2hex(random_bytes(5))),
                    bin2hex(random_bytes(16)),
                    $soirees,
                    $tarif,
                    $prix,
                    $utilisateur
                );
                $montantTotal += $prix;
            }
        }

        $montantTotal = round($montantTotal, 2);

        $commande = new Commande($idCommande, $utilisateur, new DateTimeImmutable(), $montantTotal, Commande::STATUT_EN_ATTENTE);
        foreach ($billets as $billet) {
            $commande->addBillet($billet);
        }

        $this->commandeRepository->creer($commande);

        return CommandeDTO::fromCommande($commande);
    }

    public function payerCommande(string $idCommande, string $idUtilisateur, array $carte): CommandeDTO {
        $this->validerCarte($carte);

        $commande = $this->commandeRepository->getCommandeById($idCommande);
        if ($commande === null) {
            throw new DataNotFoundException("La commande demandée n'existe pas.");
        }
        if ($commande->getUtilisateur()->getId() !== $idUtilisateur) {
            throw new AccesRefuseException("Cette commande ne vous appartient pas.");
        }
        $commande->payer();
        $this->commandeRepository->enregistrerPaiement($commande);

        return CommandeDTO::fromCommande($commande);
    }

    private function validerCarte(array $carte): void {
        $erreurs = [];

        $numero = str_replace(' ', '', (string) ($carte['numero'] ?? ''));
        if (preg_match('/^\d{16}$/', $numero) !== 1) {
            $erreurs['numero'] = "Le numéro de carte doit contenir 16 chiffres.";
        }

        $code = (string) ($carte['code'] ?? '');
        if (preg_match('/^\d{3}$/', $code) !== 1) {
            $erreurs['code'] = "Le code de sécurité doit contenir 3 chiffres.";
        }

        // Expiration au format MM/AA, et la carte doit être encore valide ce mois-ci
        $expiration = (string) ($carte['expiration'] ?? '');
        if (preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiration, $morceaux) !== 1) {
            $erreurs['expiration'] = "La date d'expiration doit être au format MM/AA.";
        } else {
            $finDuMois = new DateTimeImmutable('20' . $morceaux[2] . '-' . $morceaux[1] . '-01 +1 month');
            if ($finDuMois <= new DateTimeImmutable()) {
                $erreurs['expiration'] = "La carte est expirée.";
            }
        }

        if (count($erreurs) > 0) {
            throw new ValidationException($erreurs, implode(' ', $erreurs));
        }
    }
}
