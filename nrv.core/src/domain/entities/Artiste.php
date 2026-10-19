<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Artiste {
    public function __construct(
        private string $id,
        private string $nom
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }
}