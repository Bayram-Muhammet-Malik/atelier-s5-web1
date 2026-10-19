<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;

interface BilletRepositoryInterface {

    /**
     * @throws DataNotFoundException si aucun billet n'a ce token
     */
    public function getBilletByToken(string $token): BilletDTO;
}
