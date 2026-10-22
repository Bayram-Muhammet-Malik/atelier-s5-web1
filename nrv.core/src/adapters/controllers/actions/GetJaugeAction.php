<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use nrv\application\ports\api\ServiceJaugeInterface;

class GetJaugeAction extends AbstractAction {

    private ServiceJaugeInterface $serviceJauge;

    public function __construct(ServiceJaugeInterface $serviceJauge) {
        $this->serviceJauge = $serviceJauge;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $jauges = $this->serviceJauge->afficherJauge();

        $soirees = [];
        foreach ($jauges as $jauge) {
            $soirees[] = [
                'jauge' => $jauge,
                'links' => [
                    'soiree' => ['href' => '/soirees/' . $jauge->idSoiree],
                ],
            ];
        }

        $data = [
            'soirees' => $soirees,
            'links' => [
                'self' => ['href' => '/admin/jauge'],
            ],
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        return $rs->withStatus(200)
                  ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}
