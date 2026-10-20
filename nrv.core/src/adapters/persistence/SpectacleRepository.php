<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Style;
use nrv\domain\entities\Spectacle;

class SpectacleRepository implements SpectacleRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("
        SELECT sp.id, sp.titre, sp.description, sp.url_video, sp.horaire,
               st.id AS id_style, st.libelle AS style_libelle,
               s.id AS id_soiree, s.date_soiree
        FROM spectacle sp
        INNER JOIN style st ON sp.id_style = st.id
        INNER JOIN soiree s ON sp.id_soiree = s.id
        ORDER BY s.date_soiree ASC, sp.horaire ASC
    ");

        $allData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spectacles = [];
        foreach ($allData as $data) {
            $style = new Style((string) $data['id_style'], (string) $data['style_libelle']);

            $spectacles[] = new Spectacle(
                id: (string) $data['id'],
                titre: $data['titre'],
                description: $data['description'] ?? null,
                urlVideo: $data['url_video'] ?? null,
                dateSoiree: $data['date_soiree'] ?? null,
                horaire: $data['horaire'] ?? null,
                style: $style
            );
        }

        return $spectacles;
    }

    public function getSoireeBySpectacleId(string $id): ?Soiree
    {
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

        if (!$res)
            return null;

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
            foreach ($resArt as $art)
                $artistes[] = new Artiste((string) $art['id'], (string) $art['nom']);

            $spectacle = new Spectacle((string) $sp['spectacle_id'], (string) $sp['titre'], $sp['description'] ?? null, $sp['url_video'] ?? null, $sp['horaire'] ?? null, $style, $artistes);

            $soiree->addSpectacle($spectacle);
        }

        return $soiree;
    }
}