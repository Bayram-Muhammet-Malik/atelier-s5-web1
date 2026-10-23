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
use nrv\domain\entities\Tarif;
use nrv\domain\entities\Utilisateur;

class BilletRepository implements BilletRepositoryInterface {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getBilletByToken(string $token): Billet {
        $sql = "SELECT b.id, b.reference, b.token, b.prix_paye,
                       u.id AS id_utilisateur, u.nom, u.prenom, u.email, u.mot_de_passe, u.role,
                       s.id AS id_soiree, s.nom AS nom_soiree, s.thematique,
                       s.date_soiree, s.heure_debut, s.places_restantes,
                       l.id AS id_lieu, l.nom AS nom_lieu, l.adresse, l.nb_places,
                       t.id AS id_tarif, t.libelle AS tarif, t.prix
                FROM billet b
                JOIN commande c ON c.id = b.id_commande
                JOIN utilisateur u ON u.id = c.id_utilisateur
                JOIN billet_soiree bs ON bs.id_billet = b.id
                JOIN soiree s ON s.id = bs.id_soiree
                JOIN lieu l ON l.id = s.id_lieu
                JOIN tarif t ON t.id = b.id_tarif
                WHERE b.token = :token
                  AND c.statut = 'payee'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token]);
        $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($lignes)) {
            throw new DataNotFoundException("Billet introuvable");
        }

        $premiereLigne = $lignes[0];

        $tarif = new Tarif(
            $premiereLigne['id_tarif'],
            $premiereLigne['tarif'],
            (float) $premiereLigne['prix']
        );

        $acheteur = new Utilisateur(
            $premiereLigne['id_utilisateur'],
            $premiereLigne['nom'],
            $premiereLigne['prenom'],
            $premiereLigne['email'],
            $premiereLigne['mot_de_passe'],
            (int) $premiereLigne['role']
        );

        // On récupère toutes les soirées associées (1 pour un billet standard, plusieurs pour un pass)
        $soirees = [];
        foreach ($lignes as $ligne) {
            $lieu = new Lieu(
                $ligne['id_lieu'],
                $ligne['nom_lieu'],
                $ligne['adresse'],
                (int) $ligne['nb_places']
            );

            $soirees[] = new Soiree(
                $ligne['id_soiree'],
                $ligne['nom_soiree'],
                $ligne['thematique'],
                new DateTime($ligne['date_soiree'] . ' ' . $ligne['heure_debut']),
                (int) $ligne['places_restantes'],
                $lieu
            );
        }

        return new Billet(
            $premiereLigne['id'],
            $premiereLigne['reference'],
            $premiereLigne['token'],
            $soirees, // Tableau de soirées (ou adapté selon le constructeur de votre entité Billet)
            $tarif,
            (float) $premiereLigne['prix_paye'],
            $acheteur
        );
    }
}