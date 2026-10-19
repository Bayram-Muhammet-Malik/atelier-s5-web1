<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\application\dto\SoireeDetailDTO;
use nrv\domain\entities\Soiree;

interface SoireeRepositoryInterface {
    public function getSoireeDetailById(string $id): ?SoireeDetailDTO;
    public function getSoireeBySpectacleId(string $id): ?Soiree;
}