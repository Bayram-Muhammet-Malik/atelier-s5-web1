<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Lieu {
    public function __construct(
        private string $id,
        private string $nom,
        private string $adresse,
        private int $nbrePlace
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getAdresse(): string {
        return $this->adresse;
    }

    public function getNbrePlace(): int {
        return $this->nbrePlace;
    }
}
