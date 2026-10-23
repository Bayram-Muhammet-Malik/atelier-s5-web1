<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Billet {
    public function __construct(
        private string $id,
        private string $reference,
        private string $token,
        private array $soirees,
        private Tarif $tarif,
        private float $prixPaye,
        private ?Utilisateur $acheteur = null
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getReference(): string {
        return $this->reference;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function getSoirees(): array {
        return $this->soirees;
    }

    public function getTarif(): Tarif {
        return $this->tarif;
    }

    public function getPrixPaye(): float {
        return $this->prixPaye;
    }

    public function isPass(): bool {
        return count($this->soirees) > 1;
    }

    public function getAcheteur(): ?Utilisateur {
        return $this->acheteur;
    }
}