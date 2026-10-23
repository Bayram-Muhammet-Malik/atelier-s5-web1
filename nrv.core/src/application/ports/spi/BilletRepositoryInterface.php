<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\application\exceptions\DataNotFoundException;
use nrv\domain\entities\Billet;

interface BilletRepositoryInterface {
    public function getBilletByToken(string $token): Billet;
    public function getBilletsByUserId(string $userId): array;
}
