<?php
declare(strict_types=1);

use Slim\App;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\adapters\controllers\actions\AfficherBilletAction;
use nrv\adapters\controllers\actions\GetSoireeBySpectacleId;

return function (App $app): App {
    $app->get('/admin/jauge', AfficherJaugeAction::class);
    $app->get('/billets/{token}', AfficherBilletAction::class);
    $app->get('/soirees/{id}', \nrv\adapters\controllers\actions\AfficherSoireeAction::class);
    $app->get('/spectacle/{id}/soiree', GetSoireeBySpectacleId::class);
    return $app;
};
