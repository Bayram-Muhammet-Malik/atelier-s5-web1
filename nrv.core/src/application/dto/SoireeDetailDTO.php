<?php
declare(strict_types=1);

namespace nrv\application\dto;

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
}