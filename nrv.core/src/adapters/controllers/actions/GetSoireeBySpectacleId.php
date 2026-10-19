<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use nrv\application\ports\api\ServiceSpectacleInterface;

class GetSoireeBySpectacleId {

    private ServiceSpectacleInterface $service;

    public function __construct(ServiceSpectacleInterface $service) {
        $this->service = $service;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $soiree = $this->service->getSoireeById($args['id']);

        $rs->getBody()->write(json_encode(['soiree' => $soiree]));
        return $rs->withStatus(200)->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}
