<?php

use nrv\adapters\controllers\actions\GetSoireesAction;
use nrv\application\dto\SoireeDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceSoireeMock = $this->createMock(ServiceSoireeInterface::class);
});

it('retourne la liste des soirées au format JSON', function () {
    $soireeDTO1 = $this->createMock(SoireeDTO::class);
    $soireeDTO1->id = 'soiree-1';

    $soireeDTO2 = $this->createMock(SoireeDTO::class);
    $soireeDTO2->id = 'soiree-2';

    $this->serviceSoireeMock
        ->expects($this->once())
        ->method('getSoirees')
        ->willReturn([$soireeDTO1, $soireeDTO2]);

    $action = new GetSoireesAction($this->serviceSoireeMock);

    $request = $this->requestFactory->createRequest('GET', '/soirees');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['soirees'])->toHaveCount(2);
    expect($body['soirees'][0]['links']['soiree']['href'])->toBe('/soirees/soiree-1');
    expect($body['links']['self']['href'])->toBe('/soirees');
});

it('lève une TypeError si le service lève DataNotFoundException (variable $rq indéfinie)', function () {
    set_error_handler(fn () => true, E_WARNING | E_NOTICE);

    $this->serviceSoireeMock
        ->method('getSoirees')
        ->willThrowException(new DataNotFoundException('Aucune soirée disponible'));

    $action = new GetSoireesAction($this->serviceSoireeMock);

    $request = $this->requestFactory->createRequest('GET', '/soirees');
    $response = $this->responseFactory->createResponse();

    try {
        $action($request, $response, []);
    } finally {
        restore_error_handler();
    }
})->throws(\TypeError::class);