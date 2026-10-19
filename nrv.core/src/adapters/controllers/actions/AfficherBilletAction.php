<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceBilletInterface;

class AfficherBilletAction {

    private ServiceBilletInterface $serviceBillet;

    public function __construct(ServiceBilletInterface $serviceBillet) {
        $this->serviceBillet = $serviceBillet;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $token = $args['token'];

        try {
            $billet = $this->serviceBillet->afficherBillet($token);
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $rs->getBody()->write(json_encode(['billet' => $billet]));
        return $rs->withStatus(200)
                  ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}
