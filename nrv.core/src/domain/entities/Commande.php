<?php

declare(strict_types=1);

namespace nrv\domain\entities;

use DateTimeInterface;
use nrv\domain\exceptions\StatusChangeNotAllowedException;

class Commande
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_PAYEE = 'payee';

    public function __construct(
        private string $id,
        private Utilisateur $utilisateur,
        private DateTimeInterface $dateCommande,
        private float $montantTotal,
        private string $statut = self::STATUT_EN_ATTENTE,
        private array $billets = []
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function getDateCommande(): DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function getMontantTotal(): float
    {
        return $this->montantTotal;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getBillets(): array
    {
        return $this->billets;
    }

    public function addBillet(Billet $billet): void
    {
        $this->billets[] = $billet;
    }

    public function payer(): void
    {
        if ($this->statut !== self::STATUT_EN_ATTENTE) {
            throw new StatusChangeNotAllowedException("Cette commande a déjà été payée ou ne peut plus être payée.");
        }

        $this->statut = self::STATUT_PAYEE;
    }
}