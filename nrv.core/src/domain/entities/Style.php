<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Style {
    public function __construct(
        private string $id,
        private string $libelle
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }
}