<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\spi\BilletRepositoryInterface;

class BilletRepository implements BilletRepositoryInterface {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getBilletByToken(string $token): BilletDTO {
        // Le billet + l'acheteur + la soirée et son lieu + le tarif
        $sql = "SELECT b.reference, b.prix_paye, b.est_pass, b.id_soiree,
                       u.nom, u.prenom,
                       s.nom AS nom_soiree, s.date_soiree, s.heure_debut,
                       l.nom AS nom_lieu,
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

        return new BilletDTO(
            $ligne['reference'],
            $ligne['prenom'] . ' ' . $ligne['nom'],
            $ligne['id_soiree'],
            $ligne['nom_soiree'],
            $ligne['nom_lieu'],
            $ligne['date_soiree'],
            $ligne['heure_debut'],
            $ligne['tarif'],
            (float) $ligne['prix_paye'],
            (bool) $ligne['est_pass']
        );
    }
}
