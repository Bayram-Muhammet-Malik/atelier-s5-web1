<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\application\dto\JaugeSoireeDTO;

interface JaugeRepositoryInterface {

    /**
     * @return JaugeSoireeDTO[] la jauge de chaque soirée
     */
    public function getJaugeSoirees(): array;
}
