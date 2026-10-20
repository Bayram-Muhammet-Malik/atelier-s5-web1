<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;

use nrv\adapters\persistence\BilletRepository;
use nrv\adapters\persistence\JaugeRepository;
use nrv\adapters\persistence\SoireeRepository;
use nrv\adapters\persistence\SpectacleRepository;
use nrv\adapters\persistence\TarifRepository;

use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;

use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\api\ServiceSpectacleInterface;

use nrv\application\usecases\ServiceBillet;
use nrv\application\usecases\ServiceJauge;
use nrv\application\usecases\ServiceSoiree;
use nrv\application\usecases\ServiceSpectacle;

return [
    // Connexion PDO partagée, injectée dans les repositories
    \PDO::class => function (ContainerInterface $c) {
        $dsn = $c->get('db.driver') . ':host=' . $c->get('db.host') . ';dbname=' . $c->get('db.database') . ';charset=utf8mb4';

        return new \PDO($dsn, $c->get('db.username'), $c->get('db.password'), [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    },

    // Repo
    JaugeRepositoryInterface::class => function (ContainerInterface $c) {
        return new JaugeRepository($c->get(\PDO::class));
    },

    BilletRepositoryInterface::class => function (ContainerInterface $c) {
        return new BilletRepository($c->get(\PDO::class));
    },

    SoireeRepositoryInterface::class => function (ContainerInterface $c) {
        return new SoireeRepository($c->get(\PDO::class));
    },

    SpectacleRepositoryInterface::class => function (ContainerInterface $c) {
        return new SpectacleRepository($c->get(\PDO::class));
    },

    TarifRepositoryInterface::class => function (ContainerInterface $c) {
        return new TarifRepository($c->get(\PDO::class));
    },

    // Services
    ServiceJaugeInterface::class => function (ContainerInterface $c) {
        return new ServiceJauge($c->get(JaugeRepositoryInterface::class));
    },

    ServiceBilletInterface::class => function (ContainerInterface $c) {
        return new ServiceBillet($c->get(BilletRepositoryInterface::class));
    },

    ServiceSoireeInterface::class => function (ContainerInterface $c) {
        return new ServiceSoiree(
            $c->get(SoireeRepositoryInterface::class),
            $c->get(SpectacleRepositoryInterface::class),
            $c->get(TarifRepositoryInterface::class)
        );
    },

    ServiceSpectacleInterface::class => function (ContainerInterface $c) {
        return new ServiceSpectacle(
            $c->get(SoireeRepositoryInterface::class),
            $c->get(TarifRepositoryInterface::class),
            $c->get(SpectacleRepositoryInterface::class)
        );
    },
];
