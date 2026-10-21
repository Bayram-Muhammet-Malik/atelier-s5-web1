<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use nrv\application\usecases\AuthenticateUserUseCase;
use nrv\application\usecases\GenerateJwtUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction {
    public function __construct(
        private AuthenticateUserUseCase $authUseCase,
        private GenerateJwtUseCase $jwtUseCase
    ) {}

    public function __invoke(Request $request, Response $response): Response {
        $data = (array) $request->getParsedBody();
        try {
            $user = $this->authUseCase->execute($data['email'] ?? '', $data['mdp'] ?? '');
            $token = $this->jwtUseCase->execute($user);

            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}