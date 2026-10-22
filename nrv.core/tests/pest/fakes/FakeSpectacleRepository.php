<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\SpectacleRepositoryInterface;

// Faux repository des spectacles, rangés par soirée au lieu d'une base de données
class FakeSpectacleRepository implements SpectacleRepositoryInterface {

    public function __construct(
        private array $spectaclesParSoiree
    ) {}

    // Même comportement que le vrai repository : filtres par style et par date, puis pagination
    public function findAll(?string $style = null, ?string $date = null, ?int $limit = null, ?int $offset = null): array {
        $resultats = [];
        foreach ($this->spectaclesParSoiree as $spectacles) {
            foreach ($spectacles as $spectacle) {
                if ($style !== null && $style !== '' && $spectacle->getStyle()->getLibelle() !== $style) {
                    continue;
                }
                if ($date !== null && $date !== '' && $spectacle->getDateSoiree() !== $date) {
                    continue;
                }
                $resultats[] = $spectacle;
            }
        }

        // array_slice découpe le tableau comme LIMIT / OFFSET en SQL
        if ($limit !== null && $limit > 0) {
            $resultats = array_slice($resultats, $offset ?? 0, $limit);
        }

        return $resultats;
    }

    public function findBySoireeId(string $idSoiree): array {
        return $this->spectaclesParSoiree[$idSoiree] ?? [];
    }
}
