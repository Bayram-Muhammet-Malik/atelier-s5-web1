<?php

use nrv\application\exceptions\DataNotFoundException;
use nrv\application\usecases\ServiceBillet;
use nrv\domain\entities\Billet;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use tests\pest\fakes\FakeBilletRepository;

it('transforme le billet en DTO avec toutes les informations à imprimer', function () {
    $lieu = new Lieu('lieu-1', "L'Autre Canal", "45 boulevard d'Austrasie, 54000 Nancy", 1200);
    $soiree = new Soiree('soiree-1', 'Ouverture du festival', 'Soirée classic rock',
        new DateTime('2027-06-11 20:00:00'), 1196, $lieu);
    $billet = new Billet('billet-1', 'NRV-2027-0001', 'abc123', 'Johan Schaeffer', $soiree, 'Normal', 30.00, false);

    $service = new ServiceBillet(new FakeBilletRepository(['abc123' => $billet]));

    $dto = $service->afficherBillet('abc123');

    expect($dto->reference)->toBe('NRV-2027-0001');
    expect($dto->nomAcheteur)->toBe('Johan Schaeffer');
    expect($dto->idSoiree)->toBe('soiree-1');
    expect($dto->lieu)->toBe("L'Autre Canal");
    expect($dto->dateSoiree)->toBe('2027-06-11');
    expect($dto->heureDebut)->toBe('20:00:00');
    expect($dto->categorieTarif)->toBe('Normal');
});

it('lève une exception quand le token n\'existe pas', function () {
    $service = new ServiceBillet(new FakeBilletRepository([]));

    $service->afficherBillet('token-inconnu');
})->throws(DataNotFoundException::class);
