<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use nrv\application\ports\api\ServiceSoireeInterface;
use nrv\application\exceptions\DataNotFoundException;

class AfficherSoireeAction {
    private ServiceSoireeInterface $serviceSoiree;

    public function __construct(ServiceSoireeInterface $serviceSoiree) {
        $this->serviceSoiree = $serviceSoiree;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = $args['id'] ?? '';

        try {
            $soiree = $this->serviceSoiree->getSoireeDetail($id);
            $response->getBody()->write(json_encode($soiree));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (DataNotFoundException $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    }
}