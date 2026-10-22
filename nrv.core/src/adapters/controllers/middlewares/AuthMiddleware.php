<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use nrv\application\usecases\ServiceUserInterface;

// Laisse passer la requête seulement si l'utilisateur est connecté (jeton JWT valide)
class AuthMiddleware implements MiddlewareInterface
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');
        $jeton = str_replace('Bearer ', '', $header);

        if ($jeton === '') throw new HttpUnauthorizedException($request, "Vous devez être connecté.");

        try {
            $contenu = JWT::decode($jeton, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($request, "Session invalide ou expirée, reconnectez-vous.");
        }

        $request = $request->withAttribute('idUtilisateur', $contenu->sub);

        return $handler->handle($request);
    }
}
