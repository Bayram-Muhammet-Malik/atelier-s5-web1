<?php
declare(strict_types=1);

use Slim\App;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\adapters\controllers\actions\AfficherBilletAction;
use nrv\adapters\controllers\actions\GetSoireeBySpectacleIdAction;
use nrv\adapters\controllers\actions\GetSpectaclesAction;
use \nrv\adapters\controllers\actions\AfficherSoireeAction;

return function (App $app): App {
    $app->get('/admin/jauge', AfficherJaugeAction::class);
    $app->get('/billets/{token}', AfficherBilletAction::class);
    $app->get('/soirees/{id}', AfficherSoireeAction::class);
    $app->get('/spectacles/{id}/soiree', GetSoireeBySpectacleIdAction::class);
    $app->get('/spectacles', GetSpectaclesAction::class);
    return $app;
};
