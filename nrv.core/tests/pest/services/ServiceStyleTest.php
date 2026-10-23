<?php

use nrv\application\usecases\ServiceStyle;
use nrv\domain\entities\Style;
use tests\pest\fakes\FakeStyleRepository;

it('renvoie tous les styles de musique', function () {
    $service = new ServiceStyle(new FakeStyleRepository([
        new Style('style-1', 'Classic rock'),
        new Style('style-3', 'Métal'),
    ]));

    $styles = $service->getStyles();

    expect($styles)->toHaveCount(2);
    expect($styles[0]->id)->toBe('style-1');
    expect($styles[1]->libelle)->toBe('Métal');
});

it('renvoie une liste vide quand il n\'y a aucun style', function () {
    $service = new ServiceStyle(new FakeStyleRepository([]));

    expect($service->getStyles())->toBe([]);
});
