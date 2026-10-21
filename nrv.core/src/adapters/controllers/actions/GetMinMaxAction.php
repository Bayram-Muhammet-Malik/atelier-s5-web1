<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;

class GetMinMaxAction extends AbstractAction {
    private ServiceSoireeInterface $serviceSoiree;

    public function __construct(ServiceSoireeInterface $serviceSoiree) {
        $this->serviceSoiree = $serviceSoiree;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {

        try {
            $minmax = $this->serviceSoiree->getMinMaxDate();
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        $minDate = is_array($minmax) ? ($minmax['min_date'] ?? null) : ($minmax->minDate ?? null);
        $maxDate = is_array($minmax) ? ($minmax['max_date'] ?? null) : ($minmax->maxDate ?? null);

        return $this->json($rs, [
            'date_limits' => [
                'min' => $minDate,
                'max' => $maxDate,
            ],
        ]);
    }
}