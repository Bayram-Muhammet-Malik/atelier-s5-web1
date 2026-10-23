<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use nrv\application\ports\api\ServiceBilletInterface;

class GetMyBilletsAction extends AbstractAction
{
    private ServiceBilletInterface $serviceBillet;

    public function __construct(ServiceBilletInterface $serviceBillet)
    {
        $this->serviceBillet = $serviceBillet;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $userId = $rq->getAttribute('idUtilisateur');

        $billetsDTO = $this->serviceBillet->getBilletsByUserId($userId);

        $billetsFormates = array_map(function ($billetDTO) {
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

            $billet = get_object_vars($billetDTO);

            $billet['soirees'] = $soireesFormatees;

            return [
                'billet' => $billet,
                'links' => [
                    'self' => [
                        'href' => '/billets/' . $billetDTO->token
                    ]
                ]
            ];
        }, $billetsDTO);

        $data = [
            'billets' => $billetsFormates
        ];

        $rs->getBody()->write(
            json_encode($data, JSON_UNESCAPED_SLASHES)
        );

        return $rs
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }
}