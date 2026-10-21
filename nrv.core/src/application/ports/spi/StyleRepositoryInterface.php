<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

interface StyleRepositoryInterface {
    public function findAll(): array;
}
