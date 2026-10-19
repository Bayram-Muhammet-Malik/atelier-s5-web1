<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use DateTime;
use PDO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\domain\entities\Billet;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;

class BilletRepository implements BilletRepositoryInterface {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getBilletByToken(string $token): Billet {
        $sql = "SELECT b.id, b.reference, b.token, b.prix_paye, b.est_pass,
                       u.nom, u.prenom,
                       s.id AS id_soiree, s.nom AS nom_soiree, s.thematique,
                       s.date_soiree, s.heure_debut, s.places_restantes,
                       l.id AS id_lieu, l.nom AS nom_lieu, l.adresse, l.nb_places,
                       t.libelle AS tarif
                FROM billet b
                JOIN commande c ON c.id = b.id_commande
                JOIN utilisateur u ON u.id = c.id_utilisateur
                JOIN soiree s ON s.id = b.id_soiree
                JOIN lieu l ON l.id = s.id_lieu
                JOIN tarif t ON t.id = b.id_tarif
                WHERE b.token = :token";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $ligne = $stmt->fetch();

        if ($ligne === false) {
            throw new DataNotFoundException("Billet introuvable");
        }

        // On reconstruit les entités : le lieu, puis la soirée qui contient le lieu, puis le billet
        $lieu = new Lieu(
            $ligne['id_lieu'],
            $ligne['nom_lieu'],
            $ligne['adresse'],
            (int) $ligne['nb_places']
        );

        $soiree = new Soiree(
            $ligne['id_soiree'],
            $ligne['nom_soiree'],
            $ligne['thematique'],
            new DateTime($ligne['date_soiree'] . ' ' . $ligne['heure_debut']),
            (int) $ligne['places_restantes'],
            $lieu
        );

        return new Billet(
            $ligne['id'],
            $ligne['reference'],
            $ligne['token'],
            $ligne['prenom'] . ' ' . $ligne['nom'],
            $soiree,
            $ligne['tarif'],
            (float) $ligne['prix_paye'],
            (bool) $ligne['est_pass']
        );
    }
}
