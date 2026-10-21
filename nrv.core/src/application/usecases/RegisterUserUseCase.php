<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\domain\entities\Utilisateur;
use nrv\application\ports\spi\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;

class RegisterUserUseCase {
    public function __construct(private UserRepositoryInterface $userRepo) {}

    public function execute(string $nom, string $prenom, string $email, string $password): Utilisateur {
        // Protection Anti-XSS sur les chaînes de caractères et validation de l'email
        $cleanNom = htmlspecialchars(trim($nom), ENT_QUOTES, 'UTF-8');
        $cleanPrenom = htmlspecialchars(trim($prenom), ENT_QUOTES, 'UTF-8');
        $cleanEmail = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        if ($this->userRepo->findByEmail($cleanEmail) !== null) {
            throw new \InvalidArgumentException("Un compte existe déjà avec cette adresse email.");
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $user = new Utilisateur(
            Uuid::uuid4()->toString(),
            $cleanNom,
            $cleanPrenom,
            $cleanEmail,
            $hash,
            Utilisateur::ROLE_CLIENT
        );

        $this->userRepo->save($user);
        return $user;
    }
}