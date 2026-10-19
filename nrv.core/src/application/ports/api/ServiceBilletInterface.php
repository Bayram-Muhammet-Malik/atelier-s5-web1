<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;

interface ServiceBilletInterface {

    /**
     * @throws DataNotFoundException si aucun billet n'a ce token
     */
    public function afficherBillet(string $token): BilletDTO;
}
