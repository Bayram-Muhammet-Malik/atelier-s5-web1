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
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response(204);
        } else {
            $response = $handler->handle($request);
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $request->getHeaderLine('Origin') ?: '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
