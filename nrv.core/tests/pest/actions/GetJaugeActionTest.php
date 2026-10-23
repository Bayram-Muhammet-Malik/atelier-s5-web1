<?php

use nrv\adapters\controllers\actions\GetJaugeAction;
use nrv\application\ports\api\ServiceJaugeInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceJaugeMock = $this->createMock(ServiceJaugeInterface::class);
});

it('retourne les jauges des soirées avec leurs liens au format JSON', function () {
    $jauge1 = (object) ['idSoiree' => 'soiree-1', 'nom' => 'Soirée Rock', 'places' => 100];
    $jauge2 = (object) ['idSoiree' => 'soiree-2', 'nom' => 'Soirée Jazz', 'places' => 50];

    $this->serviceJaugeMock
        ->expects($this->once())
        ->method('afficherJauge')
        ->willReturn([$jauge1, $jauge2]);

    $action = new GetJaugeAction($this->serviceJaugeMock);

    $request = $this->requestFactory->createRequest('GET', '/admin/jauge');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['soirees'])->toHaveCount(2);
    expect($body['soirees'][0]['links']['soiree']['href'])->toBe('/soirees/soiree-1');
    expect($body['soirees'][1]['links']['soiree']['href'])->toBe('/soirees/soiree-2');
    expect($body['links']['self']['href'])->toBe('/admin/jauge');
});