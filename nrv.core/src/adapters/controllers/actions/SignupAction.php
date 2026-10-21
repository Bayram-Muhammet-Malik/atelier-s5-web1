<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use nrv\application\usecases\RegisterUserUseCase;
use nrv\application\usecases\GenerateJwtUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SigninAction {
    public function __construct(
        private RegisterUserUseCase $registerUseCase,
        private GenerateJwtUseCase $jwtUseCase
    ) {}

    public function __invoke(Request $request, Response $response): Response {
        $data = (array) $request->getParsedBody();
        try {
            $user = $this->registerUseCase->execute(
                filter_var($data['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_var($data['prenom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS),
                filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL),
                $data['mdp'] ?? ''
            );
            $token = $this->jwtUseCase->execute($user);

            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}