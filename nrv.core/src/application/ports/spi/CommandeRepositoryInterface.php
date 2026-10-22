<?php
declare(strict_types=1);

namespace nrv\application\ports\spi;

use nrv\domain\entities\Commande;
use nrv\domain\exceptions\PlacesInsuffisantesException;

interface CommandeRepositoryInterface {
    public function creer(Commande $commande): void;
    public function getCommandeById(string $id): ?Commande;
    public function enregistrerPaiement(Commande $commande): void;
}
