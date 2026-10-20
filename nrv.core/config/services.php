<?php
declare(strict_types=1);

use nrv\application\ports\spi\SpectacleRepositoryInterface;
use Psr\Container\ContainerInterface;
use nrv\adapters\persistence\JaugeRepository;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;
use nrv\application\usecases\ServiceJauge;
use nrv\adapters\persistence\BilletRepository;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\application\usecases\ServiceBillet;

use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\adapters\persistence\SoireeRepository;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\usecases\ServiceSoiree;

use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\adapters\persistence\TarifRepository;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\usecases\ServiceSpectacle;
use nrv\adapters\persistence\SpectacleRepository;

return [
    // Connexion PDO partagée, injectée dans les repositories
    \PDO::class => function (ContainerInterface $c) {
        $dsn = $c->get('db.driver') . ':host=' . $c->get('db.host') . ';dbname=' . $c->get('db.database');

        return new \PDO($dsn, $c->get('db.username'), $c->get('db.password'), [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    },

        // Jauge du back-office
    JaugeRepositoryInterface::class => function (ContainerInterface $c) {
        return new JaugeRepository($c->get(\PDO::class));
    },

    ServiceJaugeInterface::class => function (ContainerInterface $c) {
        return new ServiceJauge($c->get(JaugeRepositoryInterface::class));
    },

        // Billets
    BilletRepositoryInterface::class => function (ContainerInterface $c) {
        return new BilletRepository($c->get(\PDO::class));
    },

    ServiceBilletInterface::class => function (ContainerInterface $c) {
        return new ServiceBillet($c->get(BilletRepositoryInterface::class));
    },

        // Soirée (Fonctionnalité 2)
    SoireeRepositoryInterface::class => function (ContainerInterface $c) {
        return new SoireeRepository($c->get(\PDO::class));
    },

    ServiceSoireeInterface::class => function (ContainerInterface $c) {
        return new ServiceSoiree($c->get(SoireeRepositoryInterface::class));
    },

        // Soirée d'un spectacle (Fonctionnalité 3)
    SoireeRepositoryInterface::class => function (ContainerInterface $c) {
        return new SoireeRepository($c->get(\PDO::class));
    },

    TarifRepositoryInterface::class => function (ContainerInterface $c) {
        return new TarifRepository($c->get(\PDO::class));
    },

    ServiceSpectacleInterface::class => function (ContainerInterface $c) {
        return new ServiceSpectacle($c->get(SoireeRepositoryInterface::class), $c->get(TarifRepositoryInterface::class), $c->get(SpectacleRepositoryInterface::class));
    },

    SpectacleRepositoryInterface::class => function (ContainerInterface $c) {
        return new SpectacleRepository($c->get(\PDO::class));
    },
    
    // Inscription explicite de l'action Slim
    AfficherSoireeAction::class => function (ContainerInterface $c) {
        return new AfficherSoireeAction($c->get(ServiceSoireeInterface::class));
    },
];
