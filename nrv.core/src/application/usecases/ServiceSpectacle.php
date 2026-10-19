<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\SpectacleDTO;
use nrv\application\exceptions\DataNotFoundException;

class ServiceSpectacle implements ServiceSpectacleInterface {
    private SoireeRepositoryInterface $soireeRepository;
    private TarifRepositoryInterface $tarifRepository;

    public function __construct(
        SoireeRepositoryInterface $soireeRepository,
        TarifRepositoryInterface $tarifRepository
    ) {
        $this->soireeRepository = $soireeRepository;
        $this->tarifRepository = $tarifRepository;
    }

    public function getSoireeDetailBySpectacleId(string $idSpectacle): SoireeDetailDTO {
        $soiree = $this->soireeRepository->getSoireeBySpectacleId($idSpectacle);

        if (!$soiree) throw new DataNotFoundException("La soirée associée à ce spectacle n'existe pas.");

        $tarifs = $this->tarifRepository->findAll();
        
        $tarifNormal = 0.0;
        $tarifReduit = 0.0;

        foreach ($tarifs as $tarif) {
            $libelle = strtolower($tarif->getLibelle());
            if ($libelle === 'normal') {
                $tarifNormal = $tarif->getPrix();
            } elseif ($libelle === 'reduit' || $libelle === 'réduit') {
                $tarifReduit = $tarif->getPrix();
            }
        }

        $spectaclesDTO = [];
        foreach ($soiree->getSpectacles() as $spectacle) {
            $artistesNoms = implode(', ', array_map(fn($a) => $a->getNom(), $spectacle->getArtistes()));

            $spectaclesDTO[] = new SpectacleDTO(
                $spectacle->getId(),
                $spectacle->getTitre(),
                $artistesNoms ?: 'Inconnu',
                $spectacle->getDescription() ?? '',
                $spectacle->getStyle()->getLibelle(),
                $spectacle->getHoraire() ?? '',
                $spectacle->getUrlVideo()
            );
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
            $spectaclesDTO
        );
    }
}