<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\SoireeDTO;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\MinMaxDateDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;

class ServiceSoiree implements ServiceSoireeInterface {
    private SoireeRepositoryInterface $soireeRepository;
    private SpectacleRepositoryInterface $spectacleRepository;
    private TarifRepositoryInterface $tarifRepository;

    public function __construct(
        SoireeRepositoryInterface $soireeRepository,
        SpectacleRepositoryInterface $spectacleRepository,
        TarifRepositoryInterface $tarifRepository
    ) {
        $this->soireeRepository = $soireeRepository;
        $this->spectacleRepository = $spectacleRepository;
        $this->tarifRepository = $tarifRepository;
    }

    public function getSoirees(): array {
        $soirees = $this->soireeRepository->findAll();

        $dtos = [];
        foreach ($soirees as $soiree) $dtos[] = SoireeDTO::fromSoiree($soiree);

        return $dtos;
    }

    public function getSoireeDetail(string $id): SoireeDetailDTO {
        $soiree = $this->soireeRepository->getSoireeById($id);
        if ($soiree === null) {
            throw new DataNotFoundException("La soirée demandée n'existe pas.");
        }

        foreach ($this->spectacleRepository->findBySoireeId($soiree->getId()) as $spectacle) {
            $soiree->addSpectacle($spectacle);
        }

        return SoireeDetailDTO::fromSoiree($soiree, $this->tarifRepository->findAll());
    }

    public function getMinMaxDate(): MinMaxDateDTO {
        $dates = $this->soireeRepository->getMinMaxDate();
        return new MinMaxDateDTO($dates['min_date'], $dates['max_date']);
    }
}
