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
        // Le front envoie le jeton dans l'en-tête : "Authorization: Bearer <jeton>"
        $header = $request->getHeaderLine('Authorization');
        $jeton = str_replace('Bearer ', '', $header);

        if ($jeton === '') {
            throw new HttpUnauthorizedException($request, "Vous devez être connecté.");
        }

        // On vérifie que le jeton a bien été créé par nous (signature) et qu'il n'a pas expiré
        try {
            $contenu = JWT::decode($jeton, new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($request, "Session invalide ou expirée, reconnectez-vous.");
        }

        // On accroche l'id et le rôle de l'utilisateur à la requête, pour que les actions puissent les lire
        $request = $request->withAttribute('idUtilisateur', $contenu->sub);
        $request = $request->withAttribute('role', $contenu->role);

        // On laisse passer la requête vers l'action
        return $handler->handle($request);
    }
}
