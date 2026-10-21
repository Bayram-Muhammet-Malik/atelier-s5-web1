<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceStyleInterface;

class GetStylesAction extends AbstractAction {
    private ServiceStyleInterface $serviceStyle;

    public function __construct(ServiceStyleInterface $serviceStyle) {
        $this->serviceStyle = $serviceStyle;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {

        try {
            $styles = $this->serviceStyle->getStyles();
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $formattedStyles = [];
        foreach ($styles as $style) {
            $styleValue = $style->libelle; 

            $formattedStyles[] = [
                'style' => $style,
                'links' => [
                    'spectacles' => ['href' => '/spectacles?style=' . urlencode($styleValue)]
                ]
            ];
        }

        return $this->json($rs, [
            'styles' => $formattedStyles,
            'links' => [
                'self' => ['href' => '/styles'],
            ],
        ]);
    }
}