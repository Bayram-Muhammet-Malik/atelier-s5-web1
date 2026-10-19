<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use nrv\adapters\persistence\JaugeRepository;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;
use nrv\application\usecases\ServiceJauge;

use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\adapters\persistence\SoireeRepository;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\usecases\ServiceSoiree;

return [
    // Connexion PDO partagée, injectée dans les repositories
    \PDO::class => function (ContainerInterface $c): \PDO {
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $c->get('db.driver'),
            $c->get('db.host'),
            $c->get('db.port'),
            $c->get('db.database')
        );

        return new \PDO($dsn, $c->get('db.username'), $c->get('db.password'), [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
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

    // Soirée (Fonctionnalité 2)
    SoireeRepositoryInterface::class => function (ContainerInterface $c) {
        return new SoireeRepository($c->get(\PDO::class));
    },

    ServiceSoireeInterface::class => function (ContainerInterface $c) {
        return new ServiceSoiree($c->get(SoireeRepositoryInterface::class));
    },

    // Inscription explicite de l'action Slim
    AfficherSoireeAction::class => function (ContainerInterface $c) {
        return new AfficherSoireeAction($c->get(ServiceSoireeInterface::class));
    },
];
