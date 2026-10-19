<?php

use nrv\application\dto\JaugeSoireeDTO;
use nrv\application\usecases\ServiceJauge;
use tests\pest\fakes\FakeJaugeRepository;

it('renvoie la jauge de chaque soirée', function () {
    $repo = new FakeJaugeRepository([
        new JaugeSoireeDTO(1, 'Ouverture du festival', '2027-06-11', 1200, 4, 1196, 109.10),
        new JaugeSoireeDTO(5, "Chansons au fil de l'eau", '2027-06-16', 20, 20, 0, 560.00),
    ]);
    $service = new ServiceJauge($repo);

    $jauges = $service->afficherJauge();

    expect($jauges)->toHaveCount(2);
    expect($jauges[1]->placesVendues)->toBe(20);
    expect($jauges[1]->placesRestantes)->toBe(0);
    expect($jauges[1]->recette)->toBe(560.00);
});

it('renvoie une liste vide quand il n\'y a aucune soirée', function () {
    $service = new ServiceJauge(new FakeJaugeRepository([]));

    expect($service->afficherJauge())->toBe([]);
});
