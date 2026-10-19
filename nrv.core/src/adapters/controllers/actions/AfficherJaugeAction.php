<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use nrv\application\ports\api\ServiceJaugeInterface;

class AfficherJaugeAction {

    private ServiceJaugeInterface $serviceJauge;

    public function __construct(ServiceJaugeInterface $serviceJauge) {
        $this->serviceJauge = $serviceJauge;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $jauges = $this->serviceJauge->afficherJauge();

        $rs->getBody()->write(json_encode(['soirees' => $jauges]));
        return $rs->withStatus(200)
                  ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}
