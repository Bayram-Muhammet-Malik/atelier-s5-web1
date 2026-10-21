<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\SpectacleDTO;
use nrv\application\dto\MinMaxDateDTO;

use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;

class ServiceSpectacle implements ServiceSpectacleInterface
{
    private SoireeRepositoryInterface $soireeRepository;
    private TarifRepositoryInterface $tarifRepository;
    private SpectacleRepositoryInterface $spectacleRepository;

    public function __construct(
        SoireeRepositoryInterface $soireeRepository,
        TarifRepositoryInterface $tarifRepository,
        SpectacleRepositoryInterface $spectacleRepository
    ) {
        $this->soireeRepository = $soireeRepository;
        $this->tarifRepository = $tarifRepository;
        $this->spectacleRepository = $spectacleRepository;
    }

    public function getListeSpectacles(?string $style = null, ?string $date = null, ?int $limit = null, ?int $offset = null): array
    {
        $spectaclesDTO = [];
        foreach ($this->spectacleRepository->findAll($style, $date, $limit, $offset) as $spectacle) {
            $spectaclesDTO[] = SpectacleDTO::fromSpectacle($spectacle);
        }
        return $spectaclesDTO;
    }

    public function getSoireeDetailBySpectacleId(string $idSpectacle): SoireeDetailDTO
    {
        $soiree = $this->soireeRepository->getSoireeBySpectacleId($idSpectacle);
        if ($soiree === null) {
            throw new DataNotFoundException("La soirée associée à ce spectacle n'existe pas.");
        }

        foreach ($this->spectacleRepository->findBySoireeId($soiree->getId()) as $spectacle) {
            $soiree->addSpectacle($spectacle);
        }

        return SoireeDetailDTO::fromSoiree($soiree, $this->tarifRepository->findAll());
    }
}
