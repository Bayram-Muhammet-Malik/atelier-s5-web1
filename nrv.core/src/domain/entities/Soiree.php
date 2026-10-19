<?php
declare(strict_types=1);

namespace nrv\domain\entities;

use DateTimeInterface;

class Soiree {
    public function __construct(
        private string $id,
        private string $nom,
        private Style $theme,
        private DateTimeInterface $dateHeureDebut,
        private int $placesRestantes,
        private Lieu $lieu,
        private array $spectacles = []
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getThematique(): string {
        return $this->thematique;
    }

    public function getDateHeureDebut(): DateTimeInterface {
        return $this->dateHeureDebut;
    }

    public function getPlacesRestantes(): int {
        return $this->placesRestantes;
    }

    public function getLieu(): Lieu {
        return $this->lieu;
    }

    public function getSpectacles(): array {
        return $this->spectacles;
    }

    public function addSpectacle(Spectacle $spectacle): void {
        $this->spectacles[] = $spectacle;
    }
}