<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\SpectacleDTO;

use nrv\domain\entities\Soiree;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Style;
use nrv\domain\entities\Artiste;

class SoireeRepository implements SoireeRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getSoireeDetailById(string $id): ?SoireeDetailDTO {
        // 1. Récupération des détails de la soirée et du lieu
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.nom, s.thematique, s.date_soiree, s.heure_debut, 
                   l.nom AS lieu
            FROM soiree s
            JOIN lieu l ON s.id_lieu = l.id
            WHERE s.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $soireeData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$soireeData) {
            return null;
        }

        // 2. Récupération des tarifs (normal et réduit)
        $stmtTarifs = $this->pdo->query("SELECT libelle, prix FROM tarif");
        $tarifsData = $stmtTarifs->fetchAll(PDO::FETCH_KEY_PAIR);

        $tarifNormal = (float)($tarifsData['normal'] ?? $tarifsData['Normal'] ?? 0.0);
        $tarifReduit = (float)($tarifsData['reduit'] ?? $tarifsData['Réduit'] ?? 0.0);

        // 3. Récupération des spectacles de la soirée avec leur style et les artistes associés
        $stmtSpec = $this->pdo->prepare("
            SELECT sp.id, sp.titre, sp.description, sp.url_video, sp.horaire, 
                   st.libelle AS style,
                   COALESCE(GROUP_CONCAT(a.nom SEPARATOR ', '), 'Artiste inconnu') AS artistes
            FROM spectacle sp
            JOIN style st ON sp.id_style = st.id
            LEFT JOIN spectacle_artiste sa ON sp.id = sa.id_spectacle
            LEFT JOIN artiste a ON sa.id_artiste = a.id
            WHERE sp.id_soiree = :soiree_id
            GROUP BY sp.id, sp.titre, sp.description, sp.url_video, sp.horaire, st.libelle
            ORDER BY sp.horaire ASC
        ");
        $stmtSpec->execute(['soiree_id' => $id]);
        $spectaclesData = $stmtSpec->fetchAll(PDO::FETCH_ASSOC);

        $spectacles = [];
        foreach ($spectaclesData as $sp) {
            $spectacles[] = new SpectacleDTO(
                (string) $sp['id'],
                $sp['titre'],
                $sp['artistes'],
                $sp['description'] ?? '',
                $sp['style'],
                $sp['horaire'] ?? '',
                $sp['url_video'] ?? null
            );
        }

        return new SoireeDetailDTO(
            (string) $soireeData['id'],
            $soireeData['nom'],
            $soireeData['thematique'],
            $soireeData['date_soiree'],
            $soireeData['heure_debut'],
            $soireeData['lieu'],
            $tarifNormal,
            $tarifReduit,
            $spectacles
        );
    }

    public function getSoireeBySpectacleId(string $id): ?Soiree {
        $stmt = $this->pdo->prepare("
            SELECT s.id AS soiree_id, s.nom AS soiree_nom, s.thematique, s.date_soiree, s.heure_debut, s.places_restantes,
                   l.id AS lieu_id, l.nom AS lieu_nom, l.adresse AS lieu_adresse, l.nb_places AS lieu_nb_places
            FROM spectacle sp
            JOIN soiree s ON sp.id_soiree = s.id
            JOIN lieu l ON s.id_lieu = l.id
            WHERE sp.id = :id_spectacle
        ");
        $stmt->execute(['id_spectacle' => $id]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$res) return null;

        $lieu = new Lieu((string) $res['lieu_id'], (string) $res['lieu_nom'], (string) $res['lieu_adresse'], (int) $res['lieu_nb_places']);

        $dateHeureDebut = new \DateTime($res['date_soiree'] . ' ' . $res['heure_debut']);
        $soiree = new Soiree((string) $res['soiree_id'], (string) $res['soiree_nom'], (string) $res['thematique'], $dateHeureDebut, (int) $res['places_restantes'], $lieu);

        $stmtSpec = $this->pdo->prepare("
            SELECT sp.id AS spectacle_id, sp.titre, sp.description, sp.url_video, sp.horaire,
                   st.id AS style_id, st.libelle AS style_libelle
            FROM spectacle sp
            JOIN style st ON sp.id_style = st.id
            WHERE sp.id_soiree = :soiree_id
            ORDER BY sp.horaire ASC
        ");
        $stmtSpec->execute(['soiree_id' => $soiree->getId()]);
        $resSpec = $stmtSpec->fetchAll(\PDO::FETCH_ASSOC);

        $stmtArtistes = $this->pdo->prepare("
            SELECT a.id, a.nom
            FROM artiste a
            JOIN spectacle_artiste sa ON a.id = sa.id_artiste
            WHERE sa.id_spectacle = :spectacle_id
        ");

        foreach ($resSpec as $sp) {
            $style = new Style((string) $sp['style_id'], (string) $sp['style_libelle']);

            $stmtArtistes->execute(['spectacle_id' => $sp['spectacle_id']]);
            $resArt = $stmtArtistes->fetchAll(\PDO::FETCH_ASSOC);

            $artistes = [];
            foreach ($resArt as $art) $artistes[] = new Artiste((string) $art['id'], (string) $art['nom']);

            $spectacle = new Spectacle((string) $sp['spectacle_id'], (string) $sp['titre'], $sp['description'] ?? null, $sp['url_video'] ?? null, $sp['horaire'] ?? null, $style, $artistes);

            $soiree->addSpectacle($spectacle);
        }

        return $soiree;
    }
}