<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;

class GetSoireesAction extends AbstractAction {
    private ServiceSoireeInterface $serviceSoiree;

    public function __construct(ServiceSoireeInterface $serviceSoiree) {
        $this->serviceSoiree = $serviceSoiree;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        try {
            $soirees = $this->serviceSoiree->getSoirees();
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $formattedStyles = [];
        foreach ($soirees as $soiree) {
            $formattedStyles[] = [
                'soiree' => $soiree, 
                'links' => [
                    'soiree' => ['href' => '/soirees/' . urlencode($soiree->id)]
                ]
            ];
        }

        return $this->json($response, [
            'soirees' => $formattedStyles,
            'links' => [
                'self' => ['href' => '/soirees'],
            ],
        ]);
    }
}
