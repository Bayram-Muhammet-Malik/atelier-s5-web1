<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use nrv\adapters\persistence\JaugeRepository;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;
use nrv\application\usecases\ServiceJauge;

return [
    // Connexion PDO partagée, injectée dans les repositories
    \PDO::class => function (ContainerInterface $c) {
        $dsn = $c->get('db.driver') . ':host=' . $c->get('db.host') . ';dbname=' . $c->get('db.database');

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
];
