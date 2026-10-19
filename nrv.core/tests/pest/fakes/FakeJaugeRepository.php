<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\dto\JaugeSoireeDTO;
use nrv\application\ports\spi\JaugeRepositoryInterface;

// Faux repository : renvoie des données écrites à la main, sans base de données
class FakeJaugeRepository implements JaugeRepositoryInterface {

    private array $jauges;

    public function __construct(array $jauges) {
        $this->jauges = $jauges;
    }

    public function getJaugeSoirees(): array {
        return $this->jauges;
    }
}
