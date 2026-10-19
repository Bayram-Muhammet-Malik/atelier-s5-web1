<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\dto\JaugeSoireeDTO;
use nrv\application\ports\spi\JaugeRepositoryInterface;

class JaugeRepository implements JaugeRepositoryInterface {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getJaugeSoirees(): array {
        // Pour chaque soirée : capacité du lieu, nombre de billets vendus et total payé.
        // LEFT JOIN sur billet pour garder aussi les soirées sans aucun billet vendu.
        $sql = "SELECT s.id, s.nom, s.date_soiree, l.nb_places, s.places_restantes,
                       COUNT(b.id) AS places_vendues,
                       COALESCE(SUM(b.prix_paye), 0) AS recette
                FROM soiree s
                JOIN lieu l ON l.id = s.id_lieu
                LEFT JOIN billet b ON b.id_soiree = s.id
                GROUP BY s.id, s.nom, s.date_soiree, l.nb_places, s.places_restantes
                ORDER BY s.date_soiree";

        $lignes = $this->pdo->query($sql)->fetchAll();

        $jauges = [];
        foreach ($lignes as $ligne) {
            $jauges[] = new JaugeSoireeDTO(
                (int) $ligne['id'],
                $ligne['nom'],
                $ligne['date_soiree'],
                (int) $ligne['nb_places'],
                (int) $ligne['places_vendues'],
                (int) $ligne['places_restantes'],
                (float) $ligne['recette']
            );
        }
        return $jauges;
    }
}
