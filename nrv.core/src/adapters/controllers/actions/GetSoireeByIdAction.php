<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;

class GetSoireeByIdAction extends AbstractAction {
    private ServiceSoireeInterface $serviceSoiree;

    public function __construct(ServiceSoireeInterface $serviceSoiree) {
        $this->serviceSoiree = $serviceSoiree;
    }

    public function __invoke(Request $request, Response $response, array $args): Response {
        $id = $args['id'] ?? '';

        try {
            $soiree = $this->serviceSoiree->getSoireeDetail($id);
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        }

        return $this->json($response, [
            'soiree' => $soiree,
            'links' => [
                'self' => ['href' => '/soirees/' . urlencode($soiree->id)],
                'soirees' => ['href' => '/soirees'],
            ],
        ]);
    }
}
