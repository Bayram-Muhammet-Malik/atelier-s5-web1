<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use nrv\application\ports\api\ServiceUserInterface;

// Classe qui sert à faire passer uniquelent les admins sinon erreur 403
class AdminMiddleware implements MiddlewareInterface
{
    private ServiceUserInterface $serviceUser;

    public function __construct(ServiceUserInterface $serviceUser) {
        $this->serviceUser = $serviceUser;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uuid = $request->getAttribute('idUtilisateur');

        if (empty($uuid)) throw new HttpUnauthorizedException($request, "Non identifié.");
        if (!$this->serviceUser->isAdmin($uuid)) throw new HttpForbiddenException($request, "Accès refusé");

        return $handler->handle($request);
    }
}
