<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\domain\entities\Soiree;

// Faux repository des soirées, rangées dans des tableaux au lieu d'une base de données
class FakeSoireeRepository implements SoireeRepositoryInterface {

    public function __construct(
        private array $soirees,
        private array $soireeDuSpectacle = []
    ) {}

    public function getSoireeById(string $id): ?Soiree {
        return $this->soirees[$id] ?? null;
    }

    public function getSoireeBySpectacleId(string $idSpectacle): ?Soiree {
        $idSoiree = $this->soireeDuSpectacle[$idSpectacle] ?? null;
        if ($idSoiree === null) {
            return null;
        }
        return $this->getSoireeById($idSoiree);
    }
}
