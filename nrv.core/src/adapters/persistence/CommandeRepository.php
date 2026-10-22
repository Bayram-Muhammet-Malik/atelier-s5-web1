<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use DateTime;
use PDO;
use nrv\application\ports\spi\CommandeRepositoryInterface;
use nrv\domain\entities\Billet;
use nrv\domain\entities\Commande;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Tarif;
use nrv\domain\entities\Utilisateur;
use nrv\domain\exceptions\PlacesInsuffisantesException;

class CommandeRepository implements CommandeRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function creer(Commande $commande): void {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO commande (id, id_utilisateur, date_commande, montant_total, statut)
                VALUES (:id, :id_utilisateur, :date_commande, :montant_total, :statut)
            ");
            $stmt->execute([
                'id'             => $commande->getId(),
                'id_utilisateur' => $commande->getUtilisateur()->getId(),
                'date_commande'  => $commande->getDateCommande()->format('Y-m-d H:i:s'),
                'montant_total'  => $commande->getMontantTotal(),
                'statut'         => $commande->getStatut(),
            ]);

            $stmtBillet = $this->pdo->prepare("
                INSERT INTO billet (id, reference, token, id_commande, id_soiree, id_tarif, prix_paye, est_pass)
                VALUES (:id, :reference, :token, :id_commande, :id_soiree, :id_tarif, :prix_paye, :est_pass)
            ");
            foreach ($commande->getBillets() as $billet) {
                $stmtBillet->execute([
                    'id'          => $billet->getId(),
                    'reference'   => $billet->getReference(),
                    'token'       => $billet->getToken(),
                    'id_commande' => $commande->getId(),
                    'id_soiree'   => $billet->getSoiree()->getId(),
                    'id_tarif'    => $billet->getTarif()->getId(),
                    'prix_paye'   => $billet->getPrixPaye(),
                    'est_pass'    => $billet->isPass() ? 1 : 0,
                ]);
            }

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCommandeById(string $id): ?Commande {
        $stmt = $this->pdo->prepare("
            SELECT c.id, c.date_commande, c.montant_total, c.statut,
                   u.id AS id_utilisateur, u.nom, u.prenom, u.email, u.mot_de_passe, u.role
            FROM commande c
            JOIN utilisateur u ON u.id = c.id_utilisateur
            WHERE c.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ligne === false) {
            return null;
        }

        $utilisateur = new Utilisateur(
            $ligne['id_utilisateur'], $ligne['nom'], $ligne['prenom'],
            $ligne['email'], $ligne['mot_de_passe'], (int) $ligne['role']
        );

        $commande = new Commande(
            $ligne['id'],
            $utilisateur,
            new DateTime($ligne['date_commande']),
            (float) $ligne['montant_total'],
            $ligne['statut']
        );

        $stmtBillets = $this->pdo->prepare("
            SELECT b.id, b.reference, b.token, b.prix_paye, b.est_pass,
                   s.id AS id_soiree, s.nom AS nom_soiree, s.thematique, s.date_soiree, s.heure_debut, s.places_restantes,
                   l.id AS id_lieu, l.nom AS nom_lieu, l.adresse, l.nb_places,
                   t.id AS id_tarif, t.libelle, t.prix
            FROM billet b
            JOIN soiree s ON s.id = b.id_soiree
            JOIN lieu l ON l.id = s.id_lieu
            JOIN tarif t ON t.id = b.id_tarif
            WHERE b.id_commande = :id_commande
            ORDER BY b.reference
        ");
        $stmtBillets->execute(['id_commande' => $id]);

        foreach ($stmtBillets->fetchAll(PDO::FETCH_ASSOC) as $b) {
            $lieu = new Lieu($b['id_lieu'], $b['nom_lieu'], $b['adresse'], (int) $b['nb_places']);
            $soiree = new Soiree(
                $b['id_soiree'], $b['nom_soiree'], $b['thematique'],
                new DateTime($b['date_soiree'] . ' ' . $b['heure_debut']),
                (int) $b['places_restantes'], $lieu
            );
            $tarif = new Tarif($b['id_tarif'], $b['libelle'], (float) $b['prix']);

            $commande->addBillet(new Billet(
                $b['id'], $b['reference'], $b['token'], $soiree, $tarif,
                (float) $b['prix_paye'], (bool) $b['est_pass'], $utilisateur
            ));
        }

        return $commande;
    }

    public function enregistrerPaiement(Commande $commande): void {
        $placesParSoiree = [];
        foreach ($commande->getBillets() as $billet) {
            $idSoiree = $billet->getSoiree()->getId();
            $placesParSoiree[$idSoiree] = ($placesParSoiree[$idSoiree] ?? 0) + 1;
        }

        $this->pdo->beginTransaction();
        try {
            $stmtPlaces = $this->pdo->prepare("SELECT nom, places_restantes FROM soiree WHERE id = :id FOR UPDATE");
            $stmtRetrait = $this->pdo->prepare("UPDATE soiree SET places_restantes = places_restantes - :nb WHERE id = :id");

            foreach ($placesParSoiree as $idSoiree => $nb) {
                $stmtPlaces->execute(['id' => $idSoiree]);
                $soiree = $stmtPlaces->fetch(PDO::FETCH_ASSOC);

                if ($soiree === false || (int) $soiree['places_restantes'] < $nb) {
                    throw new PlacesInsuffisantesException(
                        "Il ne reste plus assez de places pour la soirée « " . ($soiree['nom'] ?? $idSoiree) . " »."
                    );
                }
                $stmtRetrait->execute(['nb' => $nb, 'id' => $idSoiree]);
            }

            $stmtStatut = $this->pdo->prepare("UPDATE commande SET statut = :statut WHERE id = :id");
            $stmtStatut->execute(['statut' => $commande->getStatut(), 'id' => $commande->getId()]);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
