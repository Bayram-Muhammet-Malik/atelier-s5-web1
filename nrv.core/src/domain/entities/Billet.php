<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Billet {
    public function __construct(
        private string $id,
        private string $reference,
        private string $token,
        private string $nomAcheteur,
        private Soiree $soiree,
        private string $categorieTarif,
        private float $prixPaye,
        private bool $estPass
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

    public function getNomAcheteur(): string {
        return $this->nomAcheteur;
    }

    public function getSoiree(): Soiree {
        return $this->soiree;
    }

    public function getCategorieTarif(): string {
        return $this->categorieTarif;
    }

    public function getPrixPaye(): float {
        return $this->prixPaye;
    }

    public function isPass(): bool {
        return $this->estPass;
    }
}
