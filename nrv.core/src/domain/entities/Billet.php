<?php

declare(strict_types=1);

namespace nrv\domain\entities;

class Billet
{
    public function __construct(
        private string $id,
        private string $reference,
        private string $token,
        private Soiree $soiree,
        private Tarif $tarif,
        private float $prixPaye,
        private bool $estPass = false,
        private ?Utilisateur $acheteur = null
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getSoiree(): Soiree
    {
        return $this->soiree;
    }

    public function getTarif(): Tarif
    {
        return $this->tarif;
    }

    public function getPrixPaye(): float
    {
        return $this->prixPaye;
    }

    public function isPass(): bool
    {
        return $this->estPass;
    }

    public function getAcheteur(): ?Utilisateur
    {
        return $this->acheteur;
    }
}