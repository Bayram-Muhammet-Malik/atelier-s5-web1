<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Spectacle;

class SpectacleDTO {
    public string $id;
    public string $titre;
    public string $artistes;
    public string $description;
    public string $style;
    public ?string $dateSoiree;
    public string $horaire;
    public ?string $video;
    public ?string $image;

    public function __construct(string $id, string $titre, string $artistes, string $description, string $style, ?string $dateSoiree, string $horaire, ?string $video, ?string $image = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->artistes = $artistes;
        $this->description = $description;
        $this->style = $style;
        $this->dateSoiree = $dateSoiree;
        $this->horaire = $horaire;
        $this->video = $video;
        $this->image = $image;
    }

    public static function fromSpectacle(Spectacle $spectacle): SpectacleDTO {
        $noms = [];
        foreach ($spectacle->getArtistes() as $artiste) {
            $noms[] = $artiste->getNom();
        }
        $image = null;
        $images = $spectacle->getImages();
        if (count($images) > 0) {
            $image = $images[0]->getUrl();
        }
        return new SpectacleDTO(
            $spectacle->getId(),
            $spectacle->getTitre(),
            count($noms) > 0 ? implode(', ', $noms) : 'Artiste inconnu',
            $spectacle->getDescription() ?? '',
            $spectacle->getStyle()->getLibelle(),
            $spectacle->getDateSoiree(),
            $spectacle->getHoraire() ?? '',
            $spectacle->getUrlVideo(),
            $image
        );
    }
}
