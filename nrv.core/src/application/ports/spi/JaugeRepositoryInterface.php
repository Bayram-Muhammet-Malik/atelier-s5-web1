<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\JaugeSoiree;

interface JaugeRepositoryInterface {

    /**
     * @return JaugeSoiree[] la jauge de chaque soirée
     */
    public function getJaugeSoirees(): array;
}
