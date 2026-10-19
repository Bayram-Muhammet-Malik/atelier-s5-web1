<?php
declare(strict_types=1);

use Slim\App;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\adapters\controllers\actions\AfficherBilletAction;

return function (App $app): App {

    // Routes ici
    $app->get('/admin/jauge', AfficherJaugeAction::class);
    $app->get('/billets/{token}', AfficherBilletAction::class);
    return $app;
};
