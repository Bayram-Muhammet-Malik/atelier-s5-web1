<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceBilletInterface;

class GetBilletAction extends AbstractAction {

    private ServiceBilletInterface $serviceBillet;

    public function __construct(ServiceBilletInterface $serviceBillet) {
        $this->serviceBillet = $serviceBillet;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $token = $args['token'];

        try {
            $billetDTO = $this->serviceBillet->afficherBillet($token);
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        // Construction du tableau de soirées avec leurs liens respectifs gérés ici
        $soireesFormatees = [];
        foreach ($billetDTO->soirees as $soireeData) {
            $soireesFormatees[] = [
                'soiree' => $soireeData,
                'links' => [
                    'soiree' => [
                        'href' => '/soirees/' . $soireeData['id']
                    ]
                ]
            ];
        }

        // Assemblage final de la réponse JSON
        $data = [
            'billet' => [
                'reference' => $billetDTO->reference,
                'nomAcheteur' => $billetDTO->nomAcheteur,
                'soirees' => $soireesFormatees,
                'categorieTarif' => $billetDTO->categorieTarif,
                'prixPaye' => $billetDTO->prixPaye,
                'estPass' => $billetDTO->estPass,
            ],
            'links' => [
                'self' => ['href' => '/billets/' . $token],
            ],
        ];

        $rs->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        return $rs->withStatus(200)
                  ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}