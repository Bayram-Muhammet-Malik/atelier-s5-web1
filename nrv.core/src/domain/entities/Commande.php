<?php
declare(strict_types=1);

namespace nrv\domain\entities;

use DateTimeInterface;

class Commande {
    public function __construct(
        private string $id,
        private Utilisateur $utilisateur,
        private DateTimeInterface $dateCommande,
        private float $montantTotal,
        private string $statut = 'en_attente',
        private array $billets = []
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getUtilisateur(): Utilisateur {
        return $this->utilisateur;
    }

    public function getDateCommande(): DateTimeInterface {
        return $this->dateCommande;
    }

    public function getMontantTotal(): float {
        return $this->montantTotal;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    /**
     * @return Billet[]
     */
    public function getBillets(): array {
        return $this->billets;
    }

    public function addBillet(Billet $billet): void {
        $this->billets[] = $billet;
    }
}