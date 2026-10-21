<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

interface ServiceStyleInterface {
    public function getStyles(): array;
}
