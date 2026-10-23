<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Commande;

class CommandeDTO {
    public string $id;
    public string $statut;
    public string $dateCommande;
    public float $montantTotal;
    public array $billets;

    public function __construct(string $id, string $statut, string $dateCommande, float $montantTotal, array $billets) {
        $this->id = $id;
        $this->statut = $statut;
        $this->dateCommande = $dateCommande;
        $this->montantTotal = $montantTotal;
        $this->billets = $billets;
    }

    public static function fromCommande(Commande $commande): CommandeDTO {
        $billets = [];
        foreach ($commande->getBillets() as $billet) {
            $premiereSoiree = $billet->getSoirees()[0];
            $billets[] = [
                'reference'      => $billet->getReference(),
                'token'          => $billet->getToken(),
                'nomSoiree'      => $billet->isPass() ? 'Pass 3 jours' : $premiereSoiree->getNom(),
                'dateSoiree'     => $premiereSoiree->getDateHeureDebut()->format('Y-m-d'),
                'estPass'        => $billet->isPass(),
                'categorieTarif' => $billet->getTarif()->getLibelle(),
                'prixPaye'       => $billet->getPrixPaye(),
            ];
        }

        return new CommandeDTO(
            $commande->getId(),
            $commande->getStatut(),
            $commande->getDateCommande()->format('Y-m-d H:i:s'),
            $commande->getMontantTotal(),
            $billets
        );
    }
}
