<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\SpectacleDTO;

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
}