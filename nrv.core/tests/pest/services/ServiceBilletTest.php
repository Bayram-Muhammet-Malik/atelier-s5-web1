<?php

use nrv\application\exceptions\DataNotFoundException;
use nrv\application\usecases\ServiceBillet;
use nrv\domain\entities\Billet;
use nrv\domain\entities\Lieu;
use nrv\domain\entities\Soiree;
use nrv\domain\entities\Tarif;
use nrv\domain\entities\Utilisateur;
use tests\pest\fakes\FakeBilletRepository;

// Prépare 2 billets de Johan (un billet simple et un pass 3 jours) et 1 billet de Léo
function creerServiceBillet(): ServiceBillet {
    $lieu = new Lieu('lieu-1', "L'Autre Canal", "45 boulevard d'Austrasie, 54000 Nancy", 1200);
    $soiree1 = new Soiree('soiree-1', 'Ouverture du festival', 'Soirée classic rock',
        new DateTime('2027-06-11 20:00:00'), 1196, $lieu);
    $soiree2 = new Soiree('soiree-2', 'Nuit du blues', 'Soirée blues',
        new DateTime('2027-06-12 20:30:00'), 697, $lieu);
    $soiree3 = new Soiree('soiree-3', 'Métal Fury', 'Soirée métal',
        new DateTime('2027-06-13 19:30:00'), 1200, $lieu);
    $tarif = new Tarif('tarif-1', 'Normal', 30.00);
    $johan = new Utilisateur('user-1', 'Schaeffer', 'Johan', 'johan.schaeffer@mail.fr', 'hash', 0);
    $leo = new Utilisateur('user-2', 'Thomas', 'Léo', 'leo.thomas@mail.fr', 'hash', 0);

    return new ServiceBillet(new FakeBilletRepository([
        'abc123' => new Billet('billet-1', 'NRV-2027-0001', 'abc123', [$soiree1], $tarif, 30.00, $johan),
        'pass01' => new Billet('billet-2', 'NRV-2027-0002', 'pass01', [$soiree1, $soiree2, $soiree3], $tarif, 87.30, $johan),
        'leo999' => new Billet('billet-3', 'NRV-2027-0003', 'leo999', [$soiree2], $tarif, 30.00, $leo),
    ]));
}

it('transforme le billet en DTO avec toutes les informations à imprimer', function () {
    $dto = creerServiceBillet()->afficherBillet('abc123');

    expect($dto->reference)->toBe('NRV-2027-0001');
    expect($dto->nomAcheteur)->toBe('Johan Schaeffer');
    expect($dto->categorieTarif)->toBe('Normal');
    expect($dto->estPass)->toBeFalse();
    expect($dto->soirees)->toHaveCount(1);
    expect($dto->soirees[0]['id'])->toBe('soiree-1');
    expect($dto->soirees[0]['lieu'])->toBe("L'Autre Canal");
    expect($dto->soirees[0]['date'])->toBe('2027-06-11');
    expect($dto->soirees[0]['horaire'])->toBe('20:00:00');
});

it('affiche un pass 3 jours comme un seul billet avec 3 soirées', function () {
    $dto = creerServiceBillet()->afficherBillet('pass01');

    expect($dto->estPass)->toBeTrue();
    expect($dto->soirees)->toHaveCount(3);
    expect($dto->prixPaye)->toBe(87.30);
});

it('lève une exception quand le token n\'existe pas', function () {
    creerServiceBillet()->afficherBillet('token-inconnu');
})->throws(DataNotFoundException::class);

it('renvoie seulement les billets de l\'utilisateur connecté', function () {
    $billets = creerServiceBillet()->getBilletsByUserId('user-1');

    expect($billets)->toHaveCount(2);
    expect($billets[0]->reference)->toBe('NRV-2027-0001');
    expect($billets[1]->reference)->toBe('NRV-2027-0002');
});

it('renvoie une liste vide quand l\'utilisateur n\'a aucun billet', function () {
    expect(creerServiceBillet()->getBilletsByUserId('user-inconnu'))->toBe([]);
});
