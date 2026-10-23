<?php

use nrv\domain\entities\Commande;
use nrv\domain\entities\Utilisateur;
use nrv\domain\exceptions\StatusChangeNotAllowedException;

// Une commande toute neuve, en attente de paiement
function nouvelleCommande(): Commande {
    $client = new Utilisateur('user-1', 'Schaeffer', 'Johan', 'johan@mail.fr', 'hash', Utilisateur::ROLE_CLIENT);
    return new Commande('commande-1', $client, new DateTime('2027-03-02 18:12:00'), 60.00);
}

it('crée une commande en attente de paiement par défaut', function () {
    expect(nouvelleCommande()->getStatut())->toBe(Commande::STATUT_EN_ATTENTE);
});

it('passe la commande en "payee" quand on la paie', function () {
    $commande = nouvelleCommande();

    $commande->payer();

    expect($commande->getStatut())->toBe(Commande::STATUT_PAYEE);
});

it('interdit de payer deux fois la même commande', function () {
    $commande = nouvelleCommande();

    $commande->payer();
    $commande->payer();
})->throws(StatusChangeNotAllowedException::class);
