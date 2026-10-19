<?php
declare(strict_types=1);

namespace nrv\application\dto;

class SpectacleDTO {
    public string $id;
    public string $titre;
    public string $artistes;
    public string $description;
    public string $style;
    public string $horaire;
    public ?string $video;

    public function __construct(string $id, string $titre, string $artistes, string $description, string $style, string $horaire, ?string $video = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->artistes = $artistes;
        $this->description = $description;
        $this->style = $style;
        $this->horaire = $horaire;
        $this->video = $video;
    }
}