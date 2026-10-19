<?php

use nrv\application\usecases\ServiceJauge;
use nrv\domain\entities\JaugeSoiree;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use tests\pest\fakes\FakeJaugeRepository;

it('transforme la jauge de chaque soirée en DTO', function () {
    $canal = new Lieu('lieu-1', "L'Autre Canal", "45 boulevard d'Austrasie, 54000 Nancy", 1200);
    $peniche = new Lieu('lieu-3', 'La Péniche', 'Quai de la Bataille, 54000 Nancy', 20);

    $ouverture = new Soiree('soiree-1', 'Ouverture du festival', 'Soirée classic rock',
        new DateTime('2027-06-11 20:00:00'), 1196, $canal);
    $chansons = new Soiree('soiree-5', "Chansons au fil de l'eau", 'Soirée chanson',
        new DateTime('2027-06-16 20:30:00'), 0, $peniche);

    $repo = new FakeJaugeRepository([
        new JaugeSoiree($ouverture, 4, 109.10),
        new JaugeSoiree($chansons, 20, 560.00),
    ]);
    $service = new ServiceJauge($repo);

    $jauges = $service->afficherJauge();

    expect($jauges)->toHaveCount(2);
    expect($jauges[1]->idSoiree)->toBe('soiree-5');
    expect($jauges[1]->dateSoiree)->toBe('2027-06-16');
    expect($jauges[1]->capacite)->toBe(20);
    expect($jauges[1]->placesVendues)->toBe(20);
    expect($jauges[1]->placesRestantes)->toBe(0);
    expect($jauges[1]->recette)->toBe(560.00);
});

it('renvoie une liste vide quand il n\'y a aucune soirée', function () {
    $service = new ServiceJauge(new FakeJaugeRepository([]));

    expect($service->afficherJauge())->toBe([]);
});
