<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\adapters\controllers\actions\AfficherBilletAction;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\adapters\controllers\actions\GetSoireeBySpectacleIdAction;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\adapters\controllers\actions\GetStylesAction;
use nrv\application\ports\api\ServiceStyleInterface;

return [
    // Actions ici
    AfficherJaugeAction::class => function (ContainerInterface $c) {
        return new AfficherJaugeAction($c->get(ServiceJaugeInterface::class));
    },

    AfficherBilletAction::class => function (ContainerInterface $c) {
        return new AfficherBilletAction($c->get(ServiceBilletInterface::class));
    },

    GetSoireeBySpectacleIdAction::class => function (ContainerInterface $c) {
        return new GetSoireeBySpectacleIdAction($c->get(ServiceSpectacleInterface::class));
    },

    GetStylesAction::class => function (ContainerInterface $c) {
        return new GetStylesAction($c->get(ServiceStyleInterface::class));
    },
];
