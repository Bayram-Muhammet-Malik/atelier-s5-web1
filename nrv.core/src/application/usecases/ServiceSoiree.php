<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\exceptions\DataNotFoundException;

class ServiceSoiree implements ServiceSoireeInterface {
    private SoireeRepositoryInterface $repository;

    public function __construct(SoireeRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function getSoireeDetail(string $id): SoireeDetailDTO {
        $soiree = $this->repository->getSoireeDetailById($id);
        if (!$soiree) {
            throw new DataNotFoundException("La soirée demandée n'existe pas.");
        }
        return $soiree;
    }
}