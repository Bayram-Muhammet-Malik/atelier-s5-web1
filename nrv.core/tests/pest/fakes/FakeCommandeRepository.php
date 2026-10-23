<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\CommandeRepositoryInterface;
use nrv\domain\entities\Commande;
use nrv\domain\exceptions\PlacesInsuffisantesException;

// Faux repository des commandes : garde les commandes en mémoire, dans un tableau [id => Commande]
class FakeCommandeRepository implements CommandeRepositoryInterface {
    public array $commandes = [];

    public function creer(Commande $commande): void {
        $this->commandes[$commande->getId()] = $commande;
    }

    public function getCommandeById(string $id): ?Commande {
        return $this->commandes[$id] ?? null;
    }

    // Même règle que le vrai repository : chaque soirée doit avoir assez de places
    public function enregistrerPaiement(Commande $commande): void {
        $placesParSoiree = [];
        foreach ($commande->getBillets() as $billet) {
            foreach ($billet->getSoirees() as $soiree) {
                $idSoiree = $soiree->getId();
                $placesParSoiree[$idSoiree] = ($placesParSoiree[$idSoiree] ?? 0) + 1;
            }
        }

        foreach ($commande->getBillets() as $billet) {
            foreach ($billet->getSoirees() as $soiree) {
                if ($soiree->getPlacesRestantes() < $placesParSoiree[$soiree->getId()]) {
                    throw new PlacesInsuffisantesException("Il ne reste plus assez de places.");
                }
            }
        }

        $this->commandes[$commande->getId()] = $commande;
    }
}
