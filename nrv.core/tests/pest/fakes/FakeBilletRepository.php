<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\spi\BilletRepositoryInterface;

// Faux repository : les billets sont rangés dans un tableau [token => BilletDTO]
class FakeBilletRepository implements BilletRepositoryInterface {

    private array $billets;

    public function __construct(array $billets) {
        $this->billets = $billets;
    }

    public function getBilletByToken(string $token): BilletDTO {
        if (!isset($this->billets[$token])) {
            throw new DataNotFoundException("Billet introuvable");
        }
        return $this->billets[$token];
    }
}
