<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\UserRepositoryInterface;
use nrv\domain\entities\Utilisateur;

// Faux repository des utilisateurs, rangés dans un tableau [id => Utilisateur]
class FakeUserRepository implements UserRepositoryInterface {

    public function __construct(
        private array $utilisateurs
    ) {}

    public function findByEmail(string $email): ?Utilisateur {
        foreach ($this->utilisateurs as $utilisateur) {
            if ($utilisateur->getEmail() === $email) {
                return $utilisateur;
            }
        }
        return null;
    }

    public function findById(string $id): ?Utilisateur {
        return $this->utilisateurs[$id] ?? null;
    }

    public function save(Utilisateur $user): void {
        $this->utilisateurs[$user->getId()] = $user;
    }
}
