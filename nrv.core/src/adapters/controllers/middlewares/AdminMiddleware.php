<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use nrv\domain\entities\Utilisateur;

/**
 * Classe qui sert à faire passer uniquelent les admins sinon erreur 403
 * A utiliser après le auth middleware qui add le role dans la requete
  */
class AdminMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getAttribute('role') !== Utilisateur::ROLE_ADMIN) {
            throw new HttpForbiddenException($request, "Accès réservé aux administrateurs.");
        }

        return $handler->handle($request);
    }
}
