<?php
declare(strict_types=1);

namespace app\domain\entities;

use DateTime;

class Soiree {
    public function __construct(
        private string $id,
        private string $nom,
        private string $theme,
        private DateTime $dateHeureDebut,
        private int $placesRestantes,
        private Lieu $lieu
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getTheme(): string {
        return $this->theme;
    }

    public function getDateHeureDebut(): DateTime {
        return $this->dateHeureDebut;
    }

    public function getPlacesRestantes(): int {
        return $this->placesRestantes;
    }

    public function getLieu(): Lieu {
        return $this->lieu;
    }
}