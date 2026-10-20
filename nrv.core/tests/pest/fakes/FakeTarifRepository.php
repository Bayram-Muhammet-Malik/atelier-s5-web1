<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\domain\entities\Tarif;

// Faux repository : toujours les deux mêmes tarifs, comme dans les données de test
class FakeTarifRepository implements TarifRepositoryInterface {

    public function findAll(): array {
        return [
            new Tarif('tarif-1', 'Normal', 30.00),
            new Tarif('tarif-2', 'Réduit', 20.00),
        ];
    }
}
