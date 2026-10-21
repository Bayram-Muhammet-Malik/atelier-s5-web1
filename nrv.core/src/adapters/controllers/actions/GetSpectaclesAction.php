<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use DateTimeImmutable;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use nrv\application\ports\api\ServiceSpectacleInterface;

class GetSpectaclesAction extends AbstractAction
{
    private ServiceSpectacleInterface $serviceSpectacle;

    public function __construct(ServiceSpectacleInterface $serviceSpectacle)
    {
        $this->serviceSpectacle = $serviceSpectacle;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $params = $rq->getQueryParams();

        $style = $params['style'] ?? null;
        if ($style !== null && !is_string($style)) throw new HttpBadRequestException($rq, 'Le paramètre style est invalide.');

        $date = $params['date'] ?? null;
        if ($date !== null && !is_string($date)) {
            $date = trim($date);
            $dateValidee = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
            $erreursDate = DateTimeImmutable::getLastErrors();

            if ($dateValidee === false || $dateValidee->format('Y-m-d') !== $date || ($erreursDate !== false && ($erreursDate['warning_count'] > 0 || $erreursDate['error_count'] > 0))) {
                throw new HttpBadRequestException($rq, 'La date doit respecter le format YYYY-MM-DD.');
            }
        }

        $limit = isset($params['limit']) ? (int)$params['limit'] : null;
        $offset = $limit !== null ? (int)($params['offset'] ?? 0) : null;

        $resultats = $this->serviceSpectacle->getListeSpectacles($style, $date, $limit, $offset);

        $spectacles = array_map(fn($dto) => [
            'spectacle' => $dto,
            'links' => ['soiree' => ['href' => '/spectacles/' . $dto->id . '/soiree']]
        ], $resultats);

        $queryParams = array_filter(['style' => $style, 'date' => $date]);
        $baseUrl = '/spectacles';
        $paginationLinks = [];

        if ($offset > 0) {
            $prevOffset = max(0, $offset - $limit);
            $paginationLinks['prev'] = [
                'href' => $baseUrl . '?' . http_build_query(array_merge($queryParams, ['limit' => $limit, 'offset' => $prevOffset]))
            ];
        }

        if (count($spectacles) === $limit) {
            $nextOffset = $offset + $limit;
            $paginationLinks['next'] = [
                'href' => $baseUrl . '?' . http_build_query(array_merge($queryParams, ['limit' => $limit, 'offset' => $nextOffset]))
            ];
        }

        return $this->json($rs, [
            'spectacles' => $spectacles,
            'links' => $paginationLinks
        ]);
    }
}
