<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Utilisateur {
    public const ROLE_CLIENT = 0;
    public const ROLE_ADMIN = 100;

    public function __construct(
        private string $id,
        private string $nom,
        private string $prenom,
        private string $email,
        private string $motDePasse,
        private int $role = self::ROLE_CLIENT
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getRole(): int {
        return $this->role;
    }

    public function isAdmin(): bool {
        return $this->role === self::ROLE_ADMIN;
    }
}