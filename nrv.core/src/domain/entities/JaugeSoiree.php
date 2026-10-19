<?php
declare(strict_types=1);

namespace nrv\domain\entities;

// La jauge d'une soirée : la soirée elle-même + ce qui a été vendu
class JaugeSoiree {
    public function __construct(
        private Soiree $soiree,
        private int $placesVendues,
        private float $recette
    ) {}

    public function getSoiree(): Soiree {
        return $this->soiree;
    }

    public function getPlacesVendues(): int {
        return $this->placesVendues;
    }

    public function getRecette(): float {
        return $this->recette;
    }
}
