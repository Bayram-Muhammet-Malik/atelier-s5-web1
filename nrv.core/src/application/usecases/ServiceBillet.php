<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\BilletDTO;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\spi\BilletRepositoryInterface;

class ServiceBillet implements ServiceBilletInterface {

    private BilletRepositoryInterface $billetRepository;

    public function __construct(BilletRepositoryInterface $billetRepository) {
        $this->billetRepository = $billetRepository;
    }

    public function afficherBillet(string $token): BilletDTO {
        $billet = $this->billetRepository->getBilletByToken($token);
        $soiree = $billet->getSoiree();
        $acheteur = $billet->getAcheteur();
        return new BilletDTO(
            $billet->getReference(),
            $acheteur !== null ? $acheteur->getPrenom() . ' ' . $acheteur->getNom() : '',
            $soiree->getId(),
            $soiree->getNom(),
            $soiree->getLieu()->getNom(),
            $soiree->getDateHeureDebut()->format('Y-m-d'),
            $soiree->getDateHeureDebut()->format('H:i:s'),
            $billet->getTarif()->getLibelle(),
            $billet->getPrixPaye(),
            $billet->isPass()
        );
    }
}
