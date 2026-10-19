<?php
declare(strict_types=1);

use Slim\App;
use nrv\adapters\controllers\actions\AfficherJaugeAction;

return function (App $app): App {

    // Routes ici
    $app->get('/admin/jauge', AfficherJaugeAction::class);
    $app->get('/soirees/{id}', \nrv\adapters\controllers\actions\AfficherSoireeAction::class);
    return $app;
};
