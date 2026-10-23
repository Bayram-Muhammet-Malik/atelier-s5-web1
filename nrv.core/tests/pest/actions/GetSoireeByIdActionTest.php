<?php

use nrv\adapters\controllers\actions\GetSoireeByIdAction;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSoireeInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceSoireeMock = $this->createMock(ServiceSoireeInterface::class);
});

it('retourne les détails d\'une soirée au format JSON', function () {
    $soireeDTO = $this->createMock(SoireeDetailDTO::class);
    $soireeDTO->id = 'soiree-42';

    $this->serviceSoireeMock
        ->expects($this->once())
        ->method('getSoireeDetail')
        ->with('soiree-42')
        ->willReturn($soireeDTO);

    $action = new GetSoireeByIdAction($this->serviceSoireeMock);

    $request = $this->requestFactory->createRequest('GET', '/soirees/soiree-42');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, ['id' => 'soiree-42']);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['soiree']['id'])->toBe('soiree-42');
    expect($body['links']['self']['href'])->toBe('/soirees/soiree-42');
    expect($body['links']['soirees']['href'])->toBe('/soirees');
});

it('lève une HttpNotFoundException si la soirée n\'existe pas', function () {
    $this->serviceSoireeMock
        ->method('getSoireeDetail')
        ->with('soiree-invalide')
        ->willThrowException(new DataNotFoundException('Soirée non trouvée'));

    $action = new GetSoireeByIdAction($this->serviceSoireeMock);

    $request = $this->requestFactory->createRequest('GET', '/soirees/soiree-invalide');
    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['id' => 'soiree-invalide']);
})->throws(HttpNotFoundException::class, 'Soirée non trouvée');