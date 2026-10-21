<?php
declare(strict_types=1);

namespace nrv\adapters\persistence;

use PDO;
use nrv\domain\entities\Utilisateur;
use nrv\application\ports\spi\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?Utilisateur {
        $stmt = $this->pdo->prepare("SELECT id, nom, prenom, email, mot_de_passe, role FROM utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Utilisateur($row['id'], $row['nom'], $row['prenom'], $row['email'], $row['mot_de_passe'], (int) $row['role']);
    }

    public function save(Utilisateur $user): void {
        $stmt = $this->pdo->prepare(
            "INSERT INTO utilisateur (id, nom, prenom, email, mot_de_passe, role) VALUES (:id, :nom, :prenom, :email, :motDePasse, :role)"
        );
        $stmt->execute([
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email' => $user->getEmail(),
            'motDePasse' => $user->getMotDePasse(),
            'role' => $user->getRole()
        ]);
    }
}