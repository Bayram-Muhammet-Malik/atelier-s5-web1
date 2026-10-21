<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use nrv\application\ports\api\ServiceUserInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginAction {
    private ServiceUserInterface $serviceUser;

    public function __construct(ServiceUserInterface $serviceUser) {
        $this->serviceUser = $serviceUser;
    }

    public function __invoke(Request $request, Response $response): Response {
        $data = (array) $request->getParsedBody();

        try {
            $user = $this->serviceUser->login(filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL), $data['mdp'] ?? '');
            $token = $this->serviceUser->generateJwt($user);

            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}