<?php
declare(strict_types=1);

namespace nrv\application\dto;

use app\domain\entities\Lieu;

class LieuDTO
{
    public function __construct(
        public string $id,
        public string $nom,
        public string $adresse,
        public int $nbrePlace
    ) {}

    public static function fromEntity(Lieu $lieu): self
    {
        return new self(
            $lieu->getId(),
            $lieu->getNom(),
            $lieu->getAdresse(),
            $lieu->getNbrePlace()
        );
    }

    public static function listToArray(array $lieux): array
    {
        $result = [];
        foreach ($lieux as $lieu) {
            $result[] = self::fromEntity($lieu)->toArray();
        }
        return $result;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'adresse' => $this->adresse,
            'nbrePlace' => $this->nbrePlace,
        ];
    }
}