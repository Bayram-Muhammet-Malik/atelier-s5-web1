<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\SoireeDetailDTO;

interface ServiceSpectacleInterface {
    public function getSoireeDetailBySpectacleId(string $idSpectacle): SoireeDetailDTO;
}