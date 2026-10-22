<?php
declare(strict_types=1);

use Slim\App;
use nrv\adapters\controllers\actions\AfficherJaugeAction;
use nrv\adapters\controllers\actions\AfficherBilletAction;
use nrv\adapters\controllers\actions\GetSoireeBySpectacleIdAction;
use nrv\adapters\controllers\actions\GetSpectaclesAction;
use \nrv\adapters\controllers\actions\GetSoireesAction;
use \nrv\adapters\controllers\actions\GetSoireeByIdAction;
use nrv\adapters\controllers\actions\GetStylesAction;
use nrv\adapters\controllers\actions\GetMinMaxAction;
use nrv\adapters\controllers\actions\LoginAction;
use nrv\adapters\controllers\actions\SignupAction;
use nrv\adapters\controllers\actions\CreerCommandeAction;
use nrv\adapters\controllers\actions\PayerCommandeAction;
use nrv\adapters\controllers\middlewares\AuthMiddleware;
use nrv\adapters\controllers\middlewares\AdminMiddleware;

return function (App $app): App {
    $app->get('/admin/jauge', AfficherJaugeAction::class)
        ->add(AdminMiddleware::class)
        ->add(AuthMiddleware::class);

    $app->get('/billets/{token}', AfficherBilletAction::class);
    $app->get('/soirees', GetSoireesAction::class);
    $app->get('/soirees/{id}', GetSoireeByIdAction::class);
    $app->get('/spectacles/{id}/soiree', GetSoireeBySpectacleIdAction::class);
    $app->get('/spectacles', GetSpectaclesAction::class);
    $app->get('/styles', GetStylesAction::class);
    $app->get('/dates', GetMinMaxAction::class);

    $app->post('/login', LoginAction::class);
    $app->post('/signup', SignupAction::class);
    $app->post('/commandes', CreerCommandeAction::class)
        ->add(AuthMiddleware::class);
    $app->post('/commandes/{id}/paiement', PayerCommandeAction::class)
        ->add(AuthMiddleware::class);

    return $app;
};
