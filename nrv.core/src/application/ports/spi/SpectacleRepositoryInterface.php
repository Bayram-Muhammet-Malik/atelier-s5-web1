<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\Spectacle;

interface SpectacleRepositoryInterface {

    public function findAll(): array;
    public function findBySoireeId(string $idSoiree): array;
}
