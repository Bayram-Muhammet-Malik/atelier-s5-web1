<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\domain\entities\Billet;

// Faux repository : les billets sont rangés dans un tableau [token => Billet]
class FakeBilletRepository implements BilletRepositoryInterface {

    private array $billets;

    public function __construct(array $billets) {
        $this->billets = $billets;
    }

    public function getBilletByToken(string $token): Billet {
        if (!isset($this->billets[$token])) {
            throw new DataNotFoundException("Billet introuvable");
        }
        return $this->billets[$token];
    }

    public function getBilletsByUserId(string $userId): array {
        $resultat = [];
        foreach ($this->billets as $billet) {
            if ($billet->getAcheteur() !== null && $billet->getAcheteur()->getId() === $userId) {
                $resultat[] = $billet;
            }
        }
        return $resultat;
    }
}
