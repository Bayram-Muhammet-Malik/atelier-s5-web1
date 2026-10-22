<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Soiree;
use nrv\domain\entities\Tarif;

class SoireeDTO {
    public string $id;
    public string $nom;
    public string $thematique;
    public string $date;
    public string $horaire;
    public int $placesRestantes;
    public string $lieu;

    public function __construct(string $id, string $nom, string $thematique, string $date, string $horaire, int $placesRestantes, string $lieu) {
        $this->id = $id;
        $this->nom = $nom;
        $this->thematique = $thematique;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->placesRestantes = $placesRestantes;
        $this->lieu = $lieu;
    }

    public static function fromSoiree(Soiree $soiree): SoireeDTO {
        return new SoireeDTO(
            $soiree->getId(),
            $soiree->getNom(),
            $soiree->getThematique(),
            $soiree->getDateHeureDebut()->format('Y-m-d'),
            $soiree->getDateHeureDebut()->format('H:i:s'),
            $soiree->getPlacesRestantes(),
            $soiree->getLieu()->getNom(),
        );
    }
}
