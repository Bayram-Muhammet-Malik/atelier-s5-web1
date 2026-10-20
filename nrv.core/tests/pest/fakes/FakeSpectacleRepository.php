<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\SpectacleRepositoryInterface;

// Faux repository des spectacles, rangés par soirée au lieu d'une base de données
class FakeSpectacleRepository implements SpectacleRepositoryInterface {

    public function __construct(
        private array $spectaclesParSoiree
    ) {}

    public function findAll(): array {
        $tous = [];
        foreach ($this->spectaclesParSoiree as $spectacles) {
            foreach ($spectacles as $spectacle) {
                $tous[] = $spectacle;
            }
        }
        return $tous;
    }

    public function findBySoireeId(string $idSoiree): array {
        return $this->spectaclesParSoiree[$idSoiree] ?? [];
    }
}
