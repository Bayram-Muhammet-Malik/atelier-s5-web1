<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Billet;

class BilletDTO {

    public string $reference;
    public string $nomAcheteur;
    public array $soirees;
    public string $categorieTarif;
    public float $prixPaye;
    public bool $estPass;

    public function __construct(
        string $reference,
        string $nomAcheteur,
        array $soirees,
        string $categorieTarif,
        float $prixPaye,
        bool $estPass
    ) {
        $this->reference = $reference;
        $this->nomAcheteur = $nomAcheteur;
        $this->soirees = $soirees;
        $this->categorieTarif = $categorieTarif;
        $this->prixPaye = $prixPaye;
        $this->estPass = $estPass;
    }

    public static function fromBillet(Billet $billet): self {
        $acheteur = $billet->getAcheteur();

        $soireesData = array_map(function($s) {
            return [
                'id' => $s->getId(),
                'nom' => $s->getNom(),
                'thematique' => $s->getThematique(),
                'date' => $s->getDateHeureDebut()->format('Y-m-d'),
                'horaire' => $s->getDateHeureDebut()->format('H:i:s'),
                'placesRestantes' => $s->getPlacesRestantes(),
                'lieu' => $s->getLieu()->getNom(),
            ];
        }, $billet->getSoirees());

        return new self(
            $billet->getReference(),
            $acheteur !== null ? $acheteur->getPrenom() . ' ' . $acheteur->getNom() : '',
            $soireesData,
            $billet->getTarif()->getLibelle(),
            $billet->getPrixPaye(),
            $billet->isPass()
        );
    }
}