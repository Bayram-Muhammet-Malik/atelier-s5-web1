<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use Firebase\JWT\JWT;
use nrv\domain\entities\Utilisateur;
use nrv\application\ports\spi\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;

class ServiceUser implements ServiceUserInterface {
    private UserRepositoryInterface $jaugeRepository;
    private string $secretKey;

    public function __construct(UserRepositoryInterface $userRepository, string $secretKey) {
        $this->userRepository = $userRepository;
        $this->secretKey = $secretKey;
    }

    public function login(string $email, string $password): Utilisateur {
        $user = $this->userRepo->findByEmail($email);

        if ($user === null || !password_verify($password, $user->getMotDePasse())) throw new \UnauthorizedException("Email ou mot de passe incorrect.");

        return $user;
    }

    public function register(string $nom, string $prenom, string $email, string $password): Utilisateur {
        if ($this->userRepository->findByEmail($email) !== null) throw new \InvalidArgumentException("Un compte existe déjà avec cette adresse email.");

        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $user = new Utilisateur(Uuid::uuid4()->toString(), $nom, $prenom, $email, $password, Utilisateur::ROLE_CLIENT);

        $this->userRepository->save($user);
        return $user;
    }

    public function generateJwt(Utilisateur $user): string {
        $payload = [
            'iss' => 'nrv-api',
            'iat' => time(),
            'exp' => time() + (3600 * 24), // Token 24 heures
            'sub' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}