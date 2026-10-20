<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSpectacleInterface;

class GetSpectaclesAction extends AbstractAction {
    private ServiceSpectacleInterface $serviceSpectacle;

    public function __construct(ServiceSpectacleInterface $serviceSpectacle) {
        $this->serviceSpectacle = $serviceSpectacle;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        try {
            $spectaclesDTO = $this->serviceSpectacle->getListeSpectacles();
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }
        $data = [];
        foreach ($spectaclesDTO as $dto) {
            $data[] = [
                'id' => $dto->id,
                'titre' => $dto->titre,
                'artistes' => $dto->artistes,
                'description' => $dto->description,
                'style' => $dto->style,
                'horaire' => $dto->horaire,
                'urlVideo' => $dto->urlVideo,
                'dateSoiree' => $dto->dateSoiree
            ];
        }

        $rs->getBody()->write(json_encode(['spectacles' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $rs->withStatus(200)->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}