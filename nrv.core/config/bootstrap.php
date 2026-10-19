<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use nrv\adapters\controllers\middlewares\CorsMiddleware;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$builder = new ContainerBuilder();
$builder->useAutowiring(true);

$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/services.php');
$builder->addDefinitions(__DIR__ . '/api.php');

$c = $builder->build();

$app = AppFactory::createFromContainer($c);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');
$errorHandler->registerErrorRenderer('application/json', function ($exception, $displayErrorDetails) {
    return json_encode(['message' => $exception->getMessage()]);
});

$app->add(new CorsMiddleware());

$app = (require __DIR__ . '/routes.php')($app);

return $app;
