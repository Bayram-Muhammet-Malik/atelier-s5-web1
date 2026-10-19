<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\adapters\controllers\actions\AfficherBilletAction;
use nrv\application\ports\api\ServiceBilletInterface;

return [
    // Actions ici
    AfficherJaugeAction::class => function (ContainerInterface $c) {
        return new AfficherJaugeAction($c->get(ServiceJaugeInterface::class));
    },

    AfficherBilletAction::class => function (ContainerInterface $c) {
        return new AfficherBilletAction($c->get(ServiceBilletInterface::class));
    },
];
