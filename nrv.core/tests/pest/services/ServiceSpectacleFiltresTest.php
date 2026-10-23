<?php

use nrv\application\usecases\ServiceSpectacle;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Style;
use tests\pest\fakes\FakeSoireeRepository;
use tests\pest\fakes\FakeSpectacleRepository;
use tests\pest\fakes\FakeTarifRepository;

// Prépare un service avec 5 spectacles sur 2 soirées et 3 styles différents
function creerServiceSpectacleFiltres(): ServiceSpectacle {
    $rock = new Style('style-1', 'Classic rock');
    $blues = new Style('style-2', 'Blues rock');
    $metal = new Style('style-3', 'Métal');

    $spectacles = [
        'soiree-1' => [
            new Spectacle('spec-1', 'Les Vieilles Guitares', null, null, '2027-06-11', '20:00:00', $rock),
            new Spectacle('spec-2', 'Highway 54', null, null, '2027-06-11', '21:30:00', $rock),
            new Spectacle('spec-3', 'Thunder Road Revival', null, null, '2027-06-11', '23:00:00', $blues),
        ],
        'soiree-3' => [
            new Spectacle('spec-7', 'Iron Lorraine', null, null, '2027-06-13', '19:30:00', $metal),
            new Spectacle('spec-8', 'Abyssal Forge', null, null, '2027-06-13', '21:00:00', $metal),
        ],
    ];

    return new ServiceSpectacle(new FakeSoireeRepository([]), new FakeTarifRepository(), new FakeSpectacleRepository($spectacles));
}

it('filtre les spectacles par style (fonctionnalité 5)', function () {
    $spectacles = creerServiceSpectacleFiltres()->getListeSpectacles('Métal');

    expect($spectacles)->toHaveCount(2);
    expect($spectacles[0]->titre)->toBe('Iron Lorraine');
});

it('filtre les spectacles par date (fonctionnalité 4)', function () {
    $spectacles = creerServiceSpectacleFiltres()->getListeSpectacles(null, '2027-06-11');

    expect($spectacles)->toHaveCount(3);
});

it('combine le filtre par style et le filtre par date', function () {
    $spectacles = creerServiceSpectacleFiltres()->getListeSpectacles('Blues rock', '2027-06-11');

    expect($spectacles)->toHaveCount(1);
    expect($spectacles[0]->titre)->toBe('Thunder Road Revival');
});

it('renvoie une liste vide quand aucun spectacle ne correspond', function () {
    $spectacles = creerServiceSpectacleFiltres()->getListeSpectacles('Métal', '2027-06-11');

    expect($spectacles)->toBe([]);
});

it('renvoie la première page de spectacles', function () {
    $page1 = creerServiceSpectacleFiltres()->getListeSpectacles(null, null, 2, 0);

    expect($page1)->toHaveCount(2);
    expect($page1[0]->titre)->toBe('Les Vieilles Guitares');
});

it('renvoie la page suivante grâce au décalage (offset)', function () {
    $page2 = creerServiceSpectacleFiltres()->getListeSpectacles(null, null, 2, 2);

    expect($page2)->toHaveCount(2);
    expect($page2[0]->titre)->toBe('Thunder Road Revival');
});

it('renvoie une dernière page incomplète', function () {
    $page3 = creerServiceSpectacleFiltres()->getListeSpectacles(null, null, 2, 4);

    expect($page3)->toHaveCount(1);
    expect($page3[0]->titre)->toBe('Abyssal Forge');
});
