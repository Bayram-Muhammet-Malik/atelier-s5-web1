<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\Utilisateur;

interface UserRepositoryInterface {
    public function findByEmail(string $email): ?Utilisateur;
    public function save(Utilisateur $user): void;
}