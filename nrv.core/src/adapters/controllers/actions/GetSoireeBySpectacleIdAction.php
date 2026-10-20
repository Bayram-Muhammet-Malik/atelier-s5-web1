<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSpectacleInterface;

class GetSoireeBySpectacleIdAction extends AbstractAction {
    private ServiceSpectacleInterface $serviceSpectacle;

    public function __construct(ServiceSpectacleInterface $serviceSpectacle) {
        $this->serviceSpectacle = $serviceSpectacle;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $idSpectacle = $args['id'] ?? '';

        try {
            $soireeDetail = $this->serviceSpectacle->getSoireeDetailBySpectacleId($idSpectacle);
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        return $this->json($rs, [
            'soiree' => $soireeDetail,
            'links' => [
                'self'   => ['href' => '/spectacles/' . $idSpectacle . '/soiree'],
                'soiree' => ['href' => '/soirees/' .
