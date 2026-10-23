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
        
        return BilletDTO::fromBillet($billet);
    }

    public function getBilletsByUserId(string $userId): array {
        $billets = $this->billetRepository->getBilletsByUserId($userId);

        $dtos = [];
        foreach ($billets as $billet) $dtos[] = BilletDTO::fromBillet($billet);

        return $dtos;
    }
}