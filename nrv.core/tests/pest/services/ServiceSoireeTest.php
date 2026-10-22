<?php

use nrv\application\exceptions\DataNotFoundException;
use nrv\application\usecases\ServiceSoiree;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Style;
use tests\pest\fakes\FakeSoireeRepository;
use tests\pest\fakes\FakeSpectacleRepository;
use tests\pest\fakes\FakeTarifRepository;

// Prépare un service avec une soirée qui contient 2 spectacles
function creerServiceSoiree(): ServiceSoiree {
    $soiree = new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues',
        new DateTime('2027-06-12 20:30:00'), 697, new Lieu('lieu-2', 'Salle Poirel', '3 rue Victor Poirel', 700));

    $blues = new Style('style-2', 'Blues rock');
    $spectacles = [
        'soiree-2' => [
            new Spectacle('spec-4', 'Delta Moselle', null, null, '2027-06-12', '20:30:00', $blues),
            new Spectacle('spec-5', 'Blue Stanislas', null, null, '2027-06-12', '22:00:00', $blues),
        ],
    ];

    return new ServiceSoiree(
        new FakeSoireeRepository(['soiree-2' => $soiree]),
        new FakeSpectacleRepository($spectacles),
        new FakeTarifRepository()
    );
}

function creerServiceSoireeNoDetail(): ServiceSoiree {
    $soiree = new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues', new DateTime('2027-06-12 20:30:00'), 697, new Lieu('lieu-2', 'Salle Poirel', '3 rue Victor Poirel', 700));

    return new ServiceSoiree(
        new FakeSoireeRepository(['soiree-2' => $soiree]),
        new FakeSpectacleRepository([]),
        new FakeTarifRepository()
    );
}

it('renvoie le détail d\'une soirée avec ses spectacles et ses tarifs', function () {
    $soiree = creerServiceSoiree()->getSoireeDetail('soiree-2');

    expect($soiree->nom)->toBe('Nuit du blues');
    expect($soiree->lieu)->toBe('Salle Poirel');
    expect($soiree->date)->toBe('2027-06-12');
    expect($soiree->tarifNormal)->toBe(30.00);
    expect($soiree->tarifReduit)->toBe(20.00);
    expect($soiree->spectacles)->toHaveCount(2);
    expect($soiree->spectacles[0]->titre)->toBe('Delta Moselle');
});

it('lève une exception quand la soirée n\'existe pas', function () {
    creerServiceSoiree()->getSoireeDetail('soiree-inconnue');
})->throws(DataNotFoundException::class);

it('renvoie toutes les soirées', function () {
    $soirees = creerServiceSoireeNoDetail()->getSoirees();

    expect($soirees)->toHaveCount(1);

    $soiree = $soirees[0];

    expect($soiree->nom)->toBe('Nuit du blues');
    expect($soiree->lieu)->toBe('Salle Poirel');
    expect($soiree->date)->toBe('2027-06-12');
});