<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\application\exceptions\DataNotFoundException;
use nrv\domain\entities\Billet;

interface BilletRepositoryInterface {

    /**
     * @throws DataNotFoundException si aucun billet n'a ce token
     */
    public function getBilletByToken(string $token): Billet;
}
