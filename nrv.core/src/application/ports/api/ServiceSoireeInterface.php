<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\SoireeDetailDTO;

interface ServiceSoireeInterface {
    public function getSoireeDetail(string $id): SoireeDetailDTO;
}