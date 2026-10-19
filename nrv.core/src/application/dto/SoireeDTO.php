<?php
declare(strict_types=1);

namespace nrv\application\dto;

use app\domain\entities\Soiree;

class SoireeDTO
{

      public function __construct(
            private string $id,
            private string $nom,
            private string $theme,
            private string $dateHeureDebut,
            private int $placesRestantes,
            private LieuDTO $lieu
      ) {
      }

      public static function fromEntity(Soiree $soiree): self
      {
            return new self(
                  $soiree->getId(),
                  $soiree->getNom(),
                  $soiree->getTheme(),
                  $soiree->getDateHeureDebut()->format('Y-m-d H:i:s'),
                  $soiree->getPlacesRestantes(),
                  LieuDTO::fromEntity($soiree->getLieu())
            );
      }

      public static function listToArray(array $soirees): array
      {
            $result = [];
            foreach ($soirees as $soiree) {
                  $result[] = self::fromEntity($soiree)->toArray();
            }
            return $result;
      }

      public function toArray(): array
      {
            return [
                  'id' => $this->id,
                  'nom' => $this->nom,
                  'theme' => $this->theme,
                  'dateHeureDebut' => $this->dateHeureDebut,
                  'placesRestantes' => $this->placesRestantes,
                  'lieu' => $this->lieu->toArray()
            ];
      }
}
