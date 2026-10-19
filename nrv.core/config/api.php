<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\application\ports\api\ServiceJaugeInterface;

return [
    // Actions ici
    AfficherJaugeAction::class => function (ContainerInterface $c) {
        return new AfficherJaugeAction($c->get(ServiceJaugeInterface::class));
    },
];
