<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $allowedOrigins = [
            'http://localhost:16797',
            'http://docketu.iutnc.univ-lorraine.fr:16797',
        ];

        $origin = $request->getHeaderLine('Origin');

        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response(204);
        } else {
            $response = $handler->handle($request);
        }

        $response = $response
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization');

        if (in_array($origin, $allowedOrigins, true)) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
