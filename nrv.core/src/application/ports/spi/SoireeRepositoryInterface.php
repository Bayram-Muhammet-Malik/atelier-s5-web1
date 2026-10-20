<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\Soiree;

interface SoireeRepositoryInterface {
    public function getSoireeById(string $id): ?Soiree;
    public function getSoireeBySpectacleId(string $idSpectacle): ?Soiree;
}
