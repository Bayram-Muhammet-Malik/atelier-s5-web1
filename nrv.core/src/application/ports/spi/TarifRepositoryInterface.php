<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

interface TarifRepositoryInterface {
    public function findAll(): array;
}