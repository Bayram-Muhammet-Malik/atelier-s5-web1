<?php

use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\usecases\ServiceBillet;
use tests\pest\fakes\FakeBilletRepository;

it('renvoie le billet correspondant au token', function () {
    $billet = new BilletDTO('NRV-2027-0001', 'Johan Schaeffer',
        'e2a4a751-2481-4203-9d41-3d7120db82b3', 'Ouverture du festival', "L'Autre Canal",
        '2027-06-11', '20:00:00', 'Normal', 30.00, false);
    $service = new ServiceBillet(new FakeBilletRepository(['abc123' => $billet]));

    $resultat = $service->afficherBillet('abc123');

    expect($resultat->reference)->toBe('NRV-2027-0001');
    expect($resultat->nomAcheteur)->toBe('Johan Schaeffer');
    expect($resultat->categorieTarif)->toBe('Normal');
    expect($resultat->idSoiree)->toBe('e2a4a751-2481-4203-9d41-3d7120db82b3');
});

it('lève une exception quand le token n\'existe pas', function () {
    $service = new ServiceBillet(new FakeBilletRepository([]));

    $service->afficherBillet('token-inconnu');
})->throws(DataNotFoundException::class);
