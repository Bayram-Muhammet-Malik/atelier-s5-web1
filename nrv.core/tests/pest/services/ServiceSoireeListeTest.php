<?php

use nrv\application\usecases\ServiceSoiree;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use tests\pest\fakes\FakeSoireeRepository;
use tests\pest\fakes\FakeSpectacleRepository;
use tests\pest\fakes\FakeTarifRepository;

// Prépare un service avec les soirées données (aucun spectacle, ils ne servent pas ici)
function creerServiceSoireeListe(array $soirees): ServiceSoiree {
    return new ServiceSoiree(
        new FakeSoireeRepository($soirees),
        new FakeSpectacleRepository([]),
        new FakeTarifRepository()
    );
}

function soireesDuFestival(): array {
    $poirel = new Lieu('lieu-2', 'Salle Poirel', '3 rue Victor Poirel', 700);
    return [
        'soiree-2' => new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues', new DateTime('2027-06-12 20:30:00'), 697, $poirel),
        'soiree-10' => new Soiree('soiree-10', 'Clôture : Rock en fête', 'Soirée rock', new DateTime('2027-06-24 19:00:00'), 1200, $poirel),
        'soiree-1' => new Soiree('soiree-1', 'Ouverture du festival', 'Soirée classic rock', new DateTime('2027-06-11 20:00:00'), 1196, $poirel),
    ];
}

it('renvoie la liste de toutes les soirées', function () {
    $soirees = creerServiceSoireeListe(soireesDuFestival())->getSoirees();

    expect($soirees)->toHaveCount(3);
    expect($soirees[0]->nom)->toBe('Nuit du blues');
    expect($soirees[0]->date)->toBe('2027-06-12');
    expect($soirees[0]->horaire)->toBe('20:30:00');
    expect($soirees[0]->placesRestantes)->toBe(697);
    expect($soirees[0]->lieu)->toBe('Salle Poirel');
});

it('trouve la première et la dernière date du festival', function () {
    $dates = creerServiceSoireeListe(soireesDuFestival())->getMinMaxDate();

    expect($dates->minDate)->toBe('2027-06-11');
    expect($dates->maxDate)->toBe('2027-06-24');
});

it('renvoie des dates vides quand il n\'y a encore aucune soirée', function () {
    $dates = creerServiceSoireeListe([])->getMinMaxDate();

    expect($dates->minDate)->toBe(null);
    expect($dates->maxDate)->toBe(null);
});
