<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\Soiree;

interface SpectacleRepositoryInterface {
    public function getSoireeDetail(): ?Soiree;
}