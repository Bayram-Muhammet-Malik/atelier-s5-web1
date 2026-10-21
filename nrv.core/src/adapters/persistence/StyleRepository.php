<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use nrv\application\ports\spi\StyleRepositoryInterface;
use nrv\domain\entities\Style;
use PDO;

class StyleRepository implements StyleRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT id, libelle FROM style");
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $styles = [];
        foreach ($res as $data) $styles[] = new Style((string) $data['id'], (string) $data['libelle']);

        return $styles;
    }
}