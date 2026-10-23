<?php

use nrv\application\exceptions\UnauthorizedException;
use nrv\application\usecases\ServiceUser;
use nrv\domain\entities\Utilisateur;
use tests\pest\fakes\FakeUserRepository;

// Prépare un service avec un client et un admin (mot de passe des deux : "MotDePasse2026")
function creerServiceUser(): ServiceUser {
    $hash = password_hash('MotDePasse2026', PASSWORD_BCRYPT);

    $utilisateurs = [
        'user-1' => new Utilisateur('user-1', 'Schaeffer', 'Johan', 'johan@mail.fr', $hash, Utilisateur::ROLE_CLIENT),
        'admin-1' => new Utilisateur('admin-1', 'Admin', 'NRV', 'admin@nrv.net', $hash, Utilisateur::ROLE_ADMIN),
    ];

    return new ServiceUser(new FakeUserRepository($utilisateurs), 'cle_secrete_de_test_assez_longue_pour_hs256');
}

it('connecte un utilisateur avec le bon mot de passe', function () {
    $user = creerServiceUser()->login('johan@mail.fr', 'MotDePasse2026');

    expect($user->getId())->toBe('user-1');
});

it('refuse un mauvais mot de passe', function () {
    creerServiceUser()->login('johan@mail.fr', 'MauvaisMotDePasse');
})->throws(UnauthorizedException::class);

it('refuse un email inconnu', function () {
    creerServiceUser()->login('inconnu@mail.fr', 'MotDePasse2026');
})->throws(UnauthorizedException::class);

it('ne donne jamais le mot de passe ni le hash dans le message d\'erreur', function () {
    $message = '';
    try {
        creerServiceUser()->login('johan@mail.fr', 'MauvaisMotDePasse');
    } catch (UnauthorizedException $e) {
        $message = $e->getMessage();
    }

    expect($message)->toBe('Email ou mot de passe incorrect.');
});

it('inscrit un nouvel utilisateur comme client', function () {
    $user = creerServiceUser()->register('Durand', 'Loïc', 'loic@mail.fr', 'MotDePasse2026');

    expect($user->getEmail())->toBe('loic@mail.fr');
    expect($user->getRole())->toBe(Utilisateur::ROLE_CLIENT);
});

it('enregistre le mot de passe hashé, jamais en clair', function () {
    $user = creerServiceUser()->register('Durand', 'Loïc', 'loic@mail.fr', 'MotDePasse2026');

    expect($user->getMotDePasse() === 'MotDePasse2026')->toBe(false);
    expect(password_verify('MotDePasse2026', $user->getMotDePasse()))->toBe(true);
});

it('permet de se connecter juste après l\'inscription', function () {
    $service = creerServiceUser();
    $service->register('Durand', 'Loïc', 'loic@mail.fr', 'MotDePasse2026');

    $user = $service->login('loic@mail.fr', 'MotDePasse2026');

    expect($user->getNom())->toBe('Durand');
});

it('refuse un email déjà utilisé', function () {
    creerServiceUser()->register('Schaeffer', 'Johan', 'johan@mail.fr', 'MotDePasse2026');
})->throws(InvalidArgumentException::class);

it('refuse un mot de passe de moins de 12 caractères', function () {
    creerServiceUser()->register('Durand', 'Loïc', 'loic@mail.fr', 'court');
})->throws(InvalidArgumentException::class);

it('reconnaît un administrateur', function () {
    expect(creerServiceUser()->isAdmin('admin-1'))->toBe(true);
});

it('reconnaît qu\'un client n\'est pas administrateur', function () {
    expect(creerServiceUser()->isAdmin('user-1'))->toBe(false);
});

it('répond "pas admin" pour un utilisateur qui n\'existe plus', function () {
    expect(creerServiceUser()->isAdmin('utilisateur-supprime'))->toBe(false);
});
