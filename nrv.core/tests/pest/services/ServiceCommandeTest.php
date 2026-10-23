<?php

use nrv\application\exceptions\AccesRefuseException;
use nrv\application\exceptions\ValidationException;
use nrv\application\usecases\ServiceCommande;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Utilisateur;
use nrv\domain\exceptions\PlacesInsuffisantesException;
use nrv\domain\exceptions\StatusChangeNotAllowedException;
use tests\pest\fakes\FakeCommandeRepository;
use tests\pest\fakes\FakeSoireeRepository;
use tests\pest\fakes\FakeTarifRepository;
use tests\pest\fakes\FakeUserRepository;

// Prépare un service avec 2 utilisateurs et 4 soirées (dont une presque complète : 2 places)
function creerServiceCommande(): ServiceCommande {
    $lieu = new Lieu('lieu-1', "L'Autre Canal", "45 boulevard d'Austrasie", 1200);
    $soirees = [
        'soiree-1' => new Soiree('soiree-1', 'Ouverture du festival', 'Soirée classic rock',
            new DateTime('2027-06-11 20:00:00'), 1196, $lieu),
        'soiree-2' => new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues',
            new DateTime('2027-06-12 20:30:00'), 697, $lieu),
        'soiree-3' => new Soiree('soiree-3', 'Métal Fury', 'Soirée métal',
            new DateTime('2027-06-13 19:30:00'), 1200, $lieu),
        'soiree-7' => new Soiree('soiree-7', 'Blues & Rock Session', 'Soirée blues rock',
            new DateTime('2027-06-18 20:30:00'), 2, $lieu),
    ];
    $utilisateurs = [
        'user-1' => new Utilisateur('user-1', 'Schaeffer', 'Johan', 'johan@mail.fr', 'hash', Utilisateur::ROLE_CLIENT),
        'user-2' => new Utilisateur('user-2', 'Thomas', 'Léo', 'leo@mail.fr', 'hash', Utilisateur::ROLE_CLIENT),
    ];

    return new ServiceCommande(
        new FakeCommandeRepository(),
        new FakeSoireeRepository($soirees),
        new FakeTarifRepository(),
        new FakeUserRepository($utilisateurs)
    );
}

// Une carte valide, qui expire dans un an
function carteValide(): array {
    return ['numero' => '4970 1012 3456 7890', 'expiration' => date('m/y', strtotime('+1 year')), 'code' => '123'];
}

it('crée une commande en attente avec un billet par place', function () {
    $commande = creerServiceCommande()->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 2],
        ['soireeId' => 'soiree-1', 'tarifType' => 'reduit', 'quantite' => 1],
    ]);

    expect($commande->statut)->toBe('en_attente');
    expect($commande->billets)->toHaveCount(3);
});

it('calcule le montant avec les prix de la base, pas ceux envoyés par le navigateur', function () {
    $commande = creerServiceCommande()->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 2, 'prixUnitaire' => 1],
        ['soireeId' => 'soiree-1', 'tarifType' => 'reduit', 'quantite' => 1, 'prixUnitaire' => 1],
    ]);
    expect($commande->montantTotal)->toBe(80.0);
});

it('donne à chaque billet un token différent', function () {
    $commande = creerServiceCommande()->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 3],
    ]);

    $tokens = array_column($commande->billets, 'token');
    expect(array_unique($tokens))->toHaveCount(3);
});

it('refuse un panier vide', function () {
    creerServiceCommande()->creerCommande('user-1', []);
})->throws(ValidationException::class);

it('refuse une quantité nulle ou négative', function () {
    creerServiceCommande()->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => -2],
    ]);
})->throws(ValidationException::class);

it('refuse un tarif inconnu', function () {
    creerServiceCommande()->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'gratuit', 'quantite' => 1],
    ]);
})->throws(ValidationException::class);

it('refuse une soirée inconnue', function () {
    creerServiceCommande()->creerCommande('user-1', [
        ['soireeId' => 'soiree-inconnue', 'tarifType' => 'normal', 'quantite' => 1],
    ]);
})->throws(ValidationException::class);

// ===== Fonctionnalité 9 : paiement =====

it('passe la commande en "payee" quand la carte est valide', function () {
    $service = creerServiceCommande();
    $commande = $service->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 1],
    ]);

    $payee = $service->payerCommande($commande->id, 'user-1', carteValide());

    expect($payee->statut)->toBe('payee');
});

it('refuse une carte au mauvais format', function () {
    $service = creerServiceCommande();
    $commande = $service->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 1],
    ]);

    $service->payerCommande($commande->id, 'user-1', ['numero' => '1234', 'expiration' => '13/99', 'code' => 'abc']);
})->throws(ValidationException::class);

it('refuse une carte expirée', function () {
    $service = creerServiceCommande();
    $commande = $service->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 1],
    ]);

    $service->payerCommande($commande->id, 'user-1', ['numero' => '4970101234567890', 'expiration' => '01/20', 'code' => '123']);
})->throws(ValidationException::class);

it('refuse de payer la commande de quelqu\'un d\'autre', function () {
    $service = creerServiceCommande();
    $commande = $service->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 1],
    ]);

    $service->payerCommande($commande->id, 'user-2', carteValide());
})->throws(AccesRefuseException::class);

it('refuse de payer deux fois la même commande', function () {
    $service = creerServiceCommande();
    $commande = $service->creerCommande('user-1', [
        ['soireeId' => 'soiree-1', 'tarifType' => 'normal', 'quantite' => 1],
    ]);

    $service->payerCommande($commande->id, 'user-1', carteValide());
    $service->payerCommande($commande->id, 'user-1', carteValide());
})->throws(StatusChangeNotAllowedException::class);

it('refuse le paiement quand il n\'y a plus assez de places', function () {
    $service = creerServiceCommande();
    // La soirée 7 n'a plus que 2 places
    $commande = $service->creerCommande('user-1', [
        ['soireeId' => 'soiree-7', 'tarifType' => 'normal', 'quantite' => 3],
    ]);

    $service->payerCommande($commande->id, 'user-1', carteValide());
})->throws(PlacesInsuffisantesException::class);

it('crée un pass 3 jours : un seul billet pour 3 soirées, avec 3 % de remise', function () {
    $commande = creerServiceCommande()->creerCommande('user-1', [
        ['pass' => true, 'soireeIds' => ['soiree-1', 'soiree-2', 'soiree-3'], 'tarifType' => 'normal', 'quantite' => 1],
    ]);

    expect($commande->billets)->toHaveCount(1);
    expect($commande->billets[0]['estPass'])->toBeTrue();
    expect($commande->montantTotal)->toBe(87.30); // 3 x 30 € - 3 %
});

it('accepte aussi le pass 3 jours en tarif réduit', function () {
    $commande = creerServiceCommande()->creerCommande('user-1', [
        ['pass' => true, 'soireeIds' => ['soiree-1', 'soiree-2', 'soiree-3'], 'tarifType' => 'reduit', 'quantite' => 1],
    ]);

    expect($commande->montantTotal)->toBe(58.20); // 3 x 20 € - 3 %
});

it('refuse un pass qui n\'a pas 3 soirées différentes', function () {
    creerServiceCommande()->creerCommande('user-1', [
        ['pass' => true, 'soireeIds' => ['soiree-1', 'soiree-1', 'soiree-2'], 'tarifType' => 'normal', 'quantite' => 1],
    ]);
})->throws(ValidationException::class);

it('refuse le paiement d\'un pass si une de ses soirées est complète', function () {
    $service = creerServiceCommande();
    $commande = $service->creerCommande('user-1', [
        ['pass' => true, 'soireeIds' => ['soiree-1', 'soiree-2', 'soiree-7'], 'tarifType' => 'normal', 'quantite' => 3],
    ]);

    $service->payerCommande($commande->id, 'user-1', carteValide());
})->throws(PlacesInsuffisantesException::class);
