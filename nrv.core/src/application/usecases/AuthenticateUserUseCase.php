<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\domain\entities\Utilisateur;
use nrv\application\ports\spi\UserRepositoryInterface;

class AuthenticateUserUseCase {
    public function __construct(private UserRepositoryInterface $userRepo) {}

    public function execute(string $email, string $password): Utilisateur {
        $cleanEmail = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $user = $this->userRepo->findByEmail($cleanEmail);

        if ($user === null || !password_verify($password, $user->getMotDePasse())) {
            throw new \UnauthorizedException("Email ou mot de passe incorrect.");
        }

        return $user;
    }
}