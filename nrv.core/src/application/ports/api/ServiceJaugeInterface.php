<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\JaugeSoireeDTO;

interface ServiceJaugeInterface {

    /**
     * @return JaugeSoireeDTO[] la jauge de chaque soirée
     */
    public function afficherJauge(): array;
}
