<?php

use nrv\application\exceptions\DataNotFoundException;
use nrv\application\usecases\ServiceSpectacle;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Style;
use tests\pest\fakes\FakeSoireeRepository;
use tests\pest\fakes\FakeSpectacleRepository;
use tests\pest\fakes\FakeTarifRepository;

// Prépare un service avec 2 soirées : la 1re a 2 spectacles, la 2e en a 1
function creerServiceSpectacle(): ServiceSpectacle {
    $lieu = new Lieu('lieu-1', "L'Autre Canal", "45 boulevard d'Austrasie", 1200);
    $ouverture = new Soiree('soiree-1', 'Ouverture du festival', 'Soirée classic rock',
        new DateTime('2027-06-11 20:00:00'), 1196, $lieu);
    $metal = new Soiree('soiree-3', 'Métal Fury', 'Soirée métal',
        new DateTime('2027-06-13 19:30:00'), 1200, $lieu);

    $rock = new Style('style-1', 'Classic rock');
    $metalStyle = new Style('style-3', 'Métal');

    $spectacles = [
        'soiree-1' => [
            new Spectacle('spec-1', 'Les Vieilles Guitares', null, null, '2027-06-11', '20:00:00', $rock),
            new Spectacle('spec-2', 'Highway 54', null, null, '2027-06-11', '21:30:00', $rock),
        ],
        'soiree-3' => [
            new Spectacle('spec-7', 'Iron Lorraine', null, null, '2027-06-13', '19:30:00', $metalStyle),
        ],
    ];

    return new ServiceSpectacle(
        new FakeSoireeRepository(
            ['soiree-1' => $ouverture, 'soiree-3' => $metal],
            ['spec-1' => 'soiree-1', 'spec-2' => 'soiree-1', 'spec-7' => 'soiree-3']
        ),
        new FakeTarifRepository(),
        new FakeSpectacleRepository($spectacles)
    );
}

it('renvoie la liste de tous les spectacles', function () {
    $liste = creerServiceSpectacle()->getListeSpectacles();

    expect($liste)->toHaveCount(3);
    expect($liste[0]->titre)->toBe('Les Vieilles Guitares');
    expect($liste[2]->style)->toBe('Métal');
    expect($liste[2]->dateSoiree)->toBe('2027-06-13');
});

it('renvoie le détail de la soirée d\'un spectacle avec tous ses spectacles', function () {
    $soiree = creerServiceSpectacle()->getSoireeDetailBySpectacleId('spec-2');

    expect($soiree->id)->toBe('soiree-1');
    expect($soiree->nom)->toBe('Ouverture du festival');
    expect($soiree->tarifNormal)->toBe(30.00);
    expect($soiree->tarifReduit)->toBe(20.00);
    expect($soiree->spectacles)->toHaveCount(2);
});

it('lève une exception quand le spectacle n\'existe pas', function () {
    creerServiceSpectacle()->getSoireeDetailBySpectacleId('spectacle-inconnu');
})->throws(DataNotFoundException::class);
