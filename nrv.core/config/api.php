<?php
declare(strict_types=1);

use Slim\App;
use Psr\Container\ContainerInterface;

use nrv\adapters\controllers\actions\GetJaugeAction;
use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\adapters\controllers\actions\GetBilletAction;
use nrv\application\ports\api\ServiceBilletInterface;
use nrv\adapters\controllers\actions\GetSoireeByIdAction;
use nrv\adapters\controllers\actions\GetSoireeBySpectacleIdAction;
use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\adapters\controllers\actions\GetStylesAction;
use nrv\application\ports\api\ServiceStyleInterface;
use nrv\adapters\controllers\actions\PostLoginAction;
use nrv\adapters\controllers\actions\PostSignupAction;
use nrv\application\ports\api\ServiceUserInterface;

return [
    GetJaugeAction::class => function (ContainerInterface $c) {
        return new GetJaugeAction($c->get(ServiceJaugeInterface::class));
    },

    GetBilletAction::class => function (ContainerInterface $c) {
        return new GetBilletAction($c->get(ServiceBilletInterface::class));
    },

    GetSoireesAction::class => function (ContainerInterface $c) {
        return new GetSoireesAction($c->get(ServiceSoireeInterface::class));
    },

    GetSoireesByIdAction::class => function (ContainerInterface $c) {
        return new GetSoireesAction($c->get(ServiceSoireeInterface::class));
    },

    GetSoireeBySpectacleIdAction::class => function (ContainerInterface $c) {
        return new GetSoireeBySpectacleIdAction($c->get(ServiceSpectacleInterface::class));
    },

    GetStylesAction::class => function (ContainerInterface $c) {
        return new GetStylesAction($c->get(ServiceStyleInterface::class));
    },

    GetMinMaxAction::class => function (ContainerInterface $c) {
        return new GetMinMaxAction($c->get(ServiceSoireeInterface::class));
    },

    PostLoginAction::class => function (ContainerInterface $c) {
        return new PostLoginAction($c->get(ServiceUserInterface::class));
    },

    PostSignupAction::class => function (ContainerInterface $c) {
        return new PostSignupAction($c->get(ServiceUserInterface::class));
    },
];
