<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use Firebase\JWT\JWT;
use nrv\domain\entities\Utilisateur;

class GenerateJwtUseCase {
    public function __construct(private string $secretKey) {}

    public function execute(Utilisateur $user): string {
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