<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use DateTime;
use PDO;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;

class SoireeRepository implements SoireeRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getSoireeById(string $id): ?Soiree {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.nom, s.thematique, s.date_soiree, s.heure_debut, s.places_restantes,
                   l.id AS id_lieu, l.nom AS nom_lieu, l.adresse, l.nb_places
            FROM soiree s
            JOIN lieu l ON s.id_lieu = l.id
            WHERE s.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ligne === false) {
            return null;
        }
        return $this->construireSoiree($ligne);
    }

    public function getSoireeBySpectacleId(string $idSpectacle): ?Soiree {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.nom, s.thematique, s.date_soiree, s.heure_debut, s.places_restantes,
                   l.id AS id_lieu, l.nom AS nom_lieu, l.adresse, l.nb_places
            FROM spectacle sp
            JOIN soiree s ON sp.id_soiree = s.id
            JOIN lieu l ON s.id_lieu = l.id
            WHERE sp.id = :id_spectacle
        ");
        $stmt->execute(['id_spectacle' => $idSpectacle]);
        $ligne = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ligne === false) {
            return null;
        }
        return $this->construireSoiree($ligne);
    }
    private function construireSoiree(array $ligne): Soiree {
        $lieu = new Lieu(
            (string) $ligne['id_lieu'],
            $ligne['nom_lieu'],
            $ligne['adresse'],
            (int) $ligne['nb_places']
        );

        return new Soiree(
            (string) $ligne['id'],
            $ligne['nom'],
            $ligne['thematique'],
            new DateTime($ligne['date_soiree'] . ' ' . $ligne['heure_debut']),
            (int) $ligne['places_restantes'],
            $lieu
        );
    }
}
