<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;

use nrv\adapters\persistence\BilletRepository;
use nrv\adapters\persistence\JaugeRepository;
use nrv\adapters\persistence\SoireeRepository;
use nrv\adapters\persistence\SpectacleRepository;
use nrv\adapters\persistence\TarifRepository;
use nrv\adapters\persistence\StyleRepository;
use nrv\adapters\persistence\UserRepository;
use nrv\adapters\persistence\CommandeRepository;

use nrv\application\ports\spi\BilletRepositoryInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\application\ports\spi\StyleRepositoryInterface;
use nrv\application\ports\spi\UserRepositoryInterface;
use nrv\application\ports\spi\CommandeRepositoryInterface;

use nrv\application\ports\api\ServiceBilletInterface;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\ports\api\ServiceStyleInterface;
use nrv\application\ports\api\ServiceUserInterface;
use nrv\application\ports\api\ServiceCommandeInterface;

use nrv\application\usecases\ServiceBillet;
use nrv\application\usecases\ServiceJauge;
use nrv\application\usecases\ServiceSoiree;
use nrv\application\usecases\ServiceSpectacle;
use nrv\application\usecases\ServiceStyle;
use nrv\application\usecases\ServiceUser;
use nrv\application\usecases\ServiceCommande;

use nrv\adapters\controllers\middlewares\AuthMiddleware;
use nrv\adapters\controllers\middlewares\AdminMiddleware;

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

    StyleRepositoryInterface::class => function (ContainerInterface $c) {
        return new StyleRepository($c->get(\PDO::class));
    },

    UserRepositoryInterface::class => function (ContainerInterface $c) {
        return new UserRepository($c->get(\PDO::class));
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

    ServiceStyleInterface::class => function (ContainerInterface $c) {
        return new ServiceStyle($c->get(StyleRepositoryInterface::class));
    },

    ServiceUserInterface::class => function (ContainerInterface $c) {
        return new ServiceUser(
            $c->get(UserRepositoryInterface::class),
            $c->get('jwt.secret')
        );
    },

    CommandeRepositoryInterface::class => function (ContainerInterface $c) {
        return new CommandeRepository($c->get(\PDO::class));
    },

    ServiceCommandeInterface::class => function (ContainerInterface $c) {
        return new ServiceCommande(
            $c->get(CommandeRepositoryInterface::class),
            $c->get(SoireeRepositoryInterface::class),
            $c->get(TarifRepositoryInterface::class),
            $c->get(UserRepositoryInterface::class)
        );
    },

    AuthMiddleware::class => function (ContainerInterface $c) {
        return new AuthMiddleware($c->get('jwt.secret'));
    },

    AdminMiddleware::class => function (ContainerInterface $c) {
        return new AdminMiddleware($c->get(ServiceUserInterface::class));
    },
];
