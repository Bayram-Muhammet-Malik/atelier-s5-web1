<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Spectacle {
    public function __construct(
        private string $id,
        private string $titre,
        private ?string $description,
        private ?string $urlVideo,
        private ?string $horaire,
        private Style $style,
        private array $artistes = [],
        private array $images = []
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getTitre(): string {
        return $this->titre;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getUrlVideo(): ?string {
        return $this->urlVideo;
    }

    public function getHoraire(): ?string {
        return $this->horaire;
    }

    public function getStyle(): Style {
        return $this->style;
    }

    public function getArtistes(): array {
        return $this->artistes;
    }

    public function addArtiste(Artiste $artiste): void {
        $this->artistes[] = $artiste;
    }

    public function getImages(): array {
        return $this->images;
    }

    public function addImage(Image $image): void {
        $this->images[] = $image;
    }
}