<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use DateTime;
use PDO;
use nrv\application\ports\spi\JaugeRepositoryInterface;
use nrv\domain\entities\JaugeSoiree;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;

class JaugeRepository implements JaugeRepositoryInterface {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getJaugeSoirees(): array {
        // Pour chaque soirée : capacité du lieu, nombre de billets vendus et total payé.
        // LEFT JOIN pour garder aussi les soirées sans aucun billet vendu.
        // Un pass compte pour 3 soirées : on partage son prix en 3 dans la recette.
        $sql = "SELECT s.id, s.nom, s.thematique, s.date_soiree, s.heure_debut, s.places_restantes,
                       l.id AS id_lieu, l.nom AS nom_lieu, l.adresse, l.nb_places,
                       COUNT(b.id) AS places_vendues,
                       COALESCE(ROUND(SUM(b.prix_paye / (SELECT COUNT(*) FROM billet_soiree x WHERE x.id_billet = b.id)), 2), 0) AS recette
                FROM soiree s
                JOIN lieu l ON l.id = s.id_lieu
                LEFT JOIN billet_soiree bs ON bs.id_soiree = s.id
                LEFT JOIN billet b ON b.id = bs.id_billet
                    AND b.id_commande IN (SELECT id FROM commande WHERE statut = 'payee')
                GROUP BY s.id, s.nom, s.thematique, s.date_soiree, s.heure_debut, s.places_restantes,
                         l.id, l.nom, l.adresse, l.nb_places
                ORDER BY s.date_soiree";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $lignes = $stmt->fetchAll();

        $jauges = [];
        foreach ($lignes as $ligne) {
            $lieu = new Lieu(
                $ligne['id_lieu'],
                $ligne['nom_lieu'],
                $ligne['adresse'],
                (int) $ligne['nb_places']
            );

            $soiree = new Soiree(
                $ligne['id'],
                $ligne['nom'],
                $ligne['thematique'],
                new DateTime($ligne['date_soiree'] . ' ' . $ligne['heure_debut']),
                (int) $ligne['places_restantes'],
                $lieu
            );

            $jauges[] = new JaugeSoiree(
                $soiree,
                (int) $ligne['places_vendues'],
                (float) $ligne['recette']
            );
        }
        return $jauges;
    }
}
