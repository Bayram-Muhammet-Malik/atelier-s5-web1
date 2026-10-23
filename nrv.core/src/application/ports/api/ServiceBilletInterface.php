<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;

interface ServiceBilletInterface {
    public function afficherBillet(string $token): BilletDTO;
    public function getBilletsByUserId(string $userId): array;
}
