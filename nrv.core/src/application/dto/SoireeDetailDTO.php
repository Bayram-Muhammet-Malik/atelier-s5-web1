<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Soiree;
use nrv\domain\entities\Tarif;

class SoireeDetailDTO {
    public string $id;
    public string $nom;
    public string $thematique;
    public string $date;
    public string $horaire;
    public string $lieu;
    public float $tarifNormal;
    public float $tarifReduit;
    public array $spectacles;

    public function __construct(string $id, string $nom, string $thematique, string $date, string $horaire, string $lieu, float $tarifNormal, float $tarifReduit, array $spectacles) {
        $this->id = $id;
        $this->nom = $nom;
        $this->thematique = $thematique;
        $this->date = $date;
        $this->horaire = $horaire;
        $this->lieu = $lieu;
        $this->tarifNormal = $tarifNormal;
        $this->tarifReduit = $tarifReduit;
        $this->spectacles = $spectacles;
    }
    public static function fromSoiree(Soiree $soiree, array $tarifs): SoireeDetailDTO {
        $tarifNormal = 0.0;
        $tarifReduit = 0.0;
        foreach ($tarifs as $tarif) {
            $libelle = strtolower($tarif->getLibelle());
            if ($libelle === 'normal') {
                $tarifNormal = $tarif->getPrix();
            } elseif ($libelle === 'réduit' || $libelle === 'reduit') {
                $tarifReduit = $tarif->getPrix();
            }
        }

        $spectacles = [];
        foreach ($soiree->getSpectacles() as $spectacle) {
            $spectacles[] = SpectacleDTO::fromSpectacle($spectacle);
        }

        return new SoireeDetailDTO(
            $soiree->getId(),
            $soiree->getNom(),
            $soiree->getThematique(),
            $soiree->getDateHeureDebut()->format('Y-m-d'),
            $soiree->getDateHeureDebut()->format('H:i:s'),
            $soiree->getLieu()->getNom(),
            $tarifNormal,
            $tarifReduit,
            $spectacles
        );
    }
}
