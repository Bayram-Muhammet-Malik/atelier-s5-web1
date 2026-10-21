<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\domain\entities\Artiste;
use nrv\domain\entities\Image;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Style;

class SpectacleRepository implements SpectacleRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function findAll(?string $style = null, ?string $date = null, ?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT sp.id, sp.titre, sp.description, sp.url_video, sp.horaire,
                    st.id AS id_style, st.libelle AS style_libelle, s.date_soiree
                FROM spectacle sp
                JOIN style st ON sp.id_style = st.id
                JOIN soiree s ON sp.id_soiree = s.id
                WHERE 1 = 1";
        
        $params = [];

        if ($style !== null && $style !== '') {
            $sql .= " AND st.libelle = :style";
            $params['style'] = $style;
        }

        if ($date !== null && $date !== '') {
            $sql .= " AND s.date_soiree = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY s.date_soiree ASC, sp.horaire ASC";

        if ($limit !== null && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        if ($offset !== null && $offset >= 0) {
            $sql .= " OFFSET " . (int)$offset;
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($params);

        return $this->construireSpectacles($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function findBySoireeId(string $idSoiree): array
    {
        $stmt = $this->pdo->prepare("
            SELECT sp.id, sp.titre, sp.description, sp.url_video, sp.horaire,
                   st.id AS id_style, st.libelle AS style_libelle,
                   s.date_soiree
            FROM spectacle sp
            JOIN style st ON sp.id_style = st.id
            JOIN soiree s ON sp.id_soiree = s.id
            WHERE sp.id_soiree = :id_soiree
            ORDER BY sp.horaire ASC
        ");
        $stmt->execute(['id_soiree' => $idSoiree]);

        return $this->construireSpectacles($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    // Transforme les lignes SQL en entités Spectacle, avec leurs artistes et leurs images
    private function construireSpectacles(array $lignes): array
    {
        $stmtArtistes = $this->pdo->prepare("
            SELECT a.id, a.nom
            FROM artiste a
            JOIN spectacle_artiste sa ON a.id = sa.id_artiste
            WHERE sa.id_spectacle = :id_spectacle
            ORDER BY a.nom
        ");

        $stmtImages = $this->pdo->prepare("
            SELECT id, url
            FROM image
            WHERE id_spectacle = :id_spectacle
            ORDER BY url
        ");

        $spectacles = [];
        foreach ($lignes as $ligne) {
            $spectacle = new Spectacle(
                (string) $ligne['id'],
                $ligne['titre'],
                $ligne['description'] ?? null,
                $ligne['url_video'] ?? null,
                $ligne['date_soiree'] ?? null,
                $ligne['horaire'] ?? null,
                new Style((string) $ligne['id_style'], (string) $ligne['style_libelle'])
            );

            $stmtArtistes->execute(['id_spectacle' => $ligne['id']]);
            foreach ($stmtArtistes->fetchAll(PDO::FETCH_ASSOC) as $artiste) {
                $spectacle->addArtiste(new Artiste((string) $artiste['id'], $artiste['nom']));
            }

            $stmtImages->execute(['id_spectacle' => $ligne['id']]);
            foreach ($stmtImages->fetchAll(PDO::FETCH_ASSOC) as $image) {
                $spectacle->addImage(new Image((string) $image['id'], $image['url']));
            }

            $spectacles[] = $spectacle;
        }

        return $spectacles;
    }
}
