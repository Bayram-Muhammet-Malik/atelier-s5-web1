<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use nrv\application\ports\api\ServiceSpectacleInterface;

class GetSpectaclesAction extends AbstractAction {
    private ServiceSpectacleInterface $serviceSpectacle;

    public function __construct(ServiceSpectacleInterface $serviceSpectacle) {
        $this->serviceSpectacle = $serviceSpectacle;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $spectacles = [];
        foreach ($this->serviceSpectacle->getListeSpectacles() as $dto) {
            $spectacles[] = [
                'spectacle' => $dto,
                'links' => [
                    'soiree' => ['href' => '/spectacles/' . $dto->id . '/soiree'],
                ],
            ];
        }

        return $this->json($rs, [
            'spectacles' => $spectacles,
            'links' => [
                'self' => ['href' => '/spectacles'],
            ],
        ]);
    }
}
