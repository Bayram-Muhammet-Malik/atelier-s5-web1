<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\domain\entities\Utilisateur;

interface ServiceUserInterface {
    public function login(string $email, string $password): Utilisateur;
    public function register(string $nom, string $prenom, string $email, string $password): Utilisateur;
    public function generateJwt(Utilisateur $user): string;
    public function isAdmin(string $id): bool;
}
