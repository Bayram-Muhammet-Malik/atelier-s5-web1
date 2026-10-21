<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use nrv\application\ports\api\AuthenticateUserServiceInterface;
use nrv\application\ports\api\GenerateJwtServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction {
    public function __construct(
        private AuthenticateUserServiceInterface $authUseCase,
        private GenerateJwtServiceInterface $jwtUseCase
    ) {}

    public function __invoke(Request $request, Response $response): Response {
        $data = (array) $request->getParsedBody();

        try {
            $user = $this->authUseCase->execute(filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL), $data['mdp'] ?? '');
            $token = $this->jwtUseCase->execute($user);

            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}