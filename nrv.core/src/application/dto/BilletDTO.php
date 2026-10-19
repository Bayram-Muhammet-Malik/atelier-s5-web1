<?php
declare(strict_types=1);

namespace nrv\application\dto;

class BilletDTO {

    public string $reference;
    public string $nomAcheteur;
    public string $nomSoiree;
    public string $lieu;
    public string $dateSoiree;
    public string $heureDebut;
    public string $categorieTarif;
    public float $prixPaye;
    public bool $estPass;

    public function __construct(string $reference, string $nomAcheteur, string $nomSoiree, string $lieu, string $dateSoiree, string $heureDebut, string $categorieTarif, float $prixPaye, bool $estPass) {
        $this->reference = $reference;
        $this->nomAcheteur = $nomAcheteur;
        $this->nomSoiree = $nomSoiree;
        $this->lieu = $lieu;
        $this->dateSoiree = $dateSoiree;
        $this->heureDebut = $heureDebut;
        $this->categorieTarif = $categorieTarif;
        $this->prixPaye = $prixPaye;
        $this->estPass = $estPass;
    }
}
