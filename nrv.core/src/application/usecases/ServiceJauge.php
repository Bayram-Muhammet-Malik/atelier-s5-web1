<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\JaugeSoireeDTO;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;

class ServiceJauge implements ServiceJaugeInterface {

    private JaugeRepositoryInterface $jaugeRepository;

    public function __construct(JaugeRepositoryInterface $jaugeRepository) {
        $this->jaugeRepository = $jaugeRepository;
    }

    public function afficherJauge(): array {
        $jauges = $this->jaugeRepository->getJaugeSoirees();
        $dtos = [];
        foreach ($jauges as $jauge) {
            $soiree = $jauge->getSoiree();

            $dtos[] = new JaugeSoireeDTO(
                $soiree->getId(),
                $soiree->getNom(),
                $soiree->getDateHeureDebut()->format('Y-m-d'),
                $soiree->getLieu()->getNbPlaces(),
                $jauge->getPlacesVendues(),
                $soiree->getPlacesRestantes(),
                $jauge->getRecette()
            );
        }
        return $dtos;
    }
}
