<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\MinMaxDateDTO;

interface ServiceSoireeInterface {
    public function getSoireeDetail(string $id): SoireeDetailDTO;
    public function getMinMaxDate(): MinMaxDateDTO;
}