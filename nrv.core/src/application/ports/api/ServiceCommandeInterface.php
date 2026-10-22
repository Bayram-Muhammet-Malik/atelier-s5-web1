<?php
declare(strict_types=1);

namespace nrv\application\ports\api;

use nrv\application\dto\CommandeDTO;

interface ServiceCommandeInterface {
    public function creerCommande(string $idUtilisateur, array $lignes): CommandeDTO;
    public function payerCommande(string $idCommande, string $idUtilisateur, array $carte): CommandeDTO;
}
