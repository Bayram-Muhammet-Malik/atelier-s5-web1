<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\domain\entities\Tarif;
use PDO;

class TarifRepository implements TarifRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array {
        $stmt = $this->pdo->prepare("SELECT id, libelle, prix FROM tarif");
        $stmt->execute();
        $tarifsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tarifs = [];
        foreach ($tarifsData as $data) $tarifs[] = new Tarif((string) $data['id'], (string) $data['libelle'], (float) $data['prix']);

        return $tarifs;
    }
}