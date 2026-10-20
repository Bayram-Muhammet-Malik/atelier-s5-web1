<?php

use nrv\application\dto\SoireeDetailDTO;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Spectacle;
use nrv\domain\entities\Style;
use nrv\domain\entities\Tarif;

it('copie les informations de la soirée et sépare la date et l\'heure', function () {
    $soiree = new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues',
        new DateTime('2027-06-12 20:30:00'), 697, new Lieu('lieu-2', 'Salle Poirel', '3 rue Victor Poirel', 700));

    $dto = SoireeDetailDTO::fromSoiree($soiree, []);

    expect($dto->id)->toBe('soiree-2');
    expect($dto->nom)->toBe('Nuit du blues');
    expect($dto->thematique)->toBe('Soirée blues');
    expect($dto->date)->toBe('2027-06-12');
    expect($dto->horaire)->toBe('20:30:00');
    expect($dto->lieu)->toBe('Salle Poirel');
});

it('trouve le tarif normal et le tarif réduit', function () {
    $soiree = new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues',
        new DateTime('2027-06-12 20:30:00'), 697, new Lieu('lieu-2', 'Salle Poirel', '3 rue Victor Poirel', 700));
    $tarifs = [new Tarif('tarif-1', 'Normal', 30.00), new Tarif('tarif-2', 'Réduit', 20.00)];

    $dto = SoireeDetailDTO::fromSoiree($soiree, $tarifs);

    expect($dto->tarifNormal)->toBe(30.00);
    expect($dto->tarifReduit)->toBe(20.00);
});

it('transforme aussi les spectacles de la soirée', function () {
    $soiree = new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues',
        new DateTime('2027-06-12 20:30:00'), 697, new Lieu('lieu-2', 'Salle Poirel', '3 rue Victor Poirel', 700));
    $blues = new Style('style-2', 'Blues rock');
    $soiree->addSpectacle(new Spectacle('spec-4', 'Delta Moselle', null, null, '2027-06-12', '20:30:00', $blues));
    $soiree->addSpectacle(new Spectacle('spec-5', 'Blue Stanislas', null, null, '2027-06-12', '22:00:00', $blues));

    $dto = SoireeDetailDTO::fromSoiree($soiree, []);

    expect($dto->spectacles)->toHaveCount(2);
    expect($dto->spectacles[1]->titre)->toBe('Blue Stanislas');
    expect($dto->spectacles[1]->horaire)->toBe('22:00:00');
});
