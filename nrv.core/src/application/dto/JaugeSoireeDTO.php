<?php
declare(strict_types=1);

namespace nrv\application\dto;

// Les chiffres de la jauge pour une soirée (affichés dans le back-office)
class JaugeSoireeDTO {

    public string $idSoiree;
    public string $nomSoiree;
    public string $dateSoiree;
    public int $capacite;
    public int $placesVendues;
    public int $placesRestantes;
    public float $recette;

       public function __construct(string $idSoiree, string $nomSoiree, string $dateSoiree, int $capacite, int $placesVendues, int $placesRestantes, float $recette) {
        $this->idSoiree = $idSoiree;
        $this->nomSoiree = $nomSoiree;
        $this->dateSoiree = $dateSoiree;
        $this->capacite = $capacite;
        $this->placesVendues = $placesVendues;
        $this->placesRestantes = $placesRestantes;
        $this->recette = $recette;
    }
}
