<?php

use nrv\application\dto\SpectacleDTO;
use nrv\domain\entities\Artiste;
use nrv\domain\entities\Image;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Style;

it('copie toutes les informations du spectacle dans le DTO', function () {
    $spectacle = new Spectacle('spec-1', 'Delta Moselle', 'Un duo guitare-voix.', 'https://videos.nrv.net/4.mp4',
        '2027-06-12', '20:30:00', new Style('style-2', 'Blues rock'));

    $dto = SpectacleDTO::fromSpectacle($spectacle);

    expect($dto->id)->toBe('spec-1');
    expect($dto->titre)->toBe('Delta Moselle');
    expect($dto->description)->toBe('Un duo guitare-voix.');
    expect($dto->style)->toBe('Blues rock');
    expect($dto->dateSoiree)->toBe('2027-06-12');
    expect($dto->horaire)->toBe('20:30:00');
    expect($dto->video)->toBe('https://videos.nrv.net/4.mp4');
});

it('réunit les noms des artistes séparés par des virgules', function () {
    $spectacle = new Spectacle('spec-1', 'Delta Moselle', null, null, '2027-06-12', '20:30:00',
        new Style('style-2', 'Blues rock'),
        [new Artiste('a-1', 'Anna Weber'), new Artiste('a-2', 'Paul Kieffer')]);

    $dto = SpectacleDTO::fromSpectacle($spectacle);

    expect($dto->artistes)->toBe('Anna Weber, Paul Kieffer');
});

it('indique "Artiste inconnu" quand le spectacle n\'a pas d\'artiste', function () {
    $spectacle = new Spectacle('spec-1', 'Delta Moselle', null, null, '2027-06-12', '20:30:00',
        new Style('style-2', 'Blues rock'));

    $dto = SpectacleDTO::fromSpectacle($spectacle);

    expect($dto->artistes)->toBe('Artiste inconnu');
});

it('garde la première image du spectacle', function () {
    $spectacle = new Spectacle('spec-1', 'Delta Moselle', null, null, '2027-06-12', '20:30:00',
        new Style('style-2', 'Blues rock'), [],
        [new Image('img-1', 'img/spectacles/spectacle-4-1.jpg'), new Image('img-2', 'img/spectacles/spectacle-4-2.jpg')]);

    $dto = SpectacleDTO::fromSpectacle($spectacle);

    expect($dto->image)->toBe('img/spectacles/spectacle-4-1.jpg');
});

it('met l\'image à null quand le spectacle n\'en a pas', function () {
    $spectacle = new Spectacle('spec-1', 'Delta Moselle', null, null, '2027-06-12', '20:30:00',
        new Style('style-2', 'Blues rock'));

    $dto = SpectacleDTO::fromSpectacle($spectacle);

    expect($dto->image)->toBe(null);
});