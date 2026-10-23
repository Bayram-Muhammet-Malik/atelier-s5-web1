<?php

use nrv\adapters\controllers\actions\GetSoireeBySpectacleIdAction;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceSpectacleInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceSpectacleMock = $this->createMock(ServiceSpectacleInterface::class);
});

it('retourne la soirée associée à un spectacle au format JSON', function () {
    $soireeDetailDTO = $this->createMock(SoireeDetailDTO::class);
    $soireeDetailDTO->id = 'soiree-10';

    $this->serviceSpectacleMock
        ->expects($this->once())
        ->method('getSoireeDetailBySpectacleId')
        ->with('spec-5')
        ->willReturn($soireeDetailDTO);

    $action = new GetSoireeBySpectacleIdAction($this->serviceSpectacleMock);

    $request = $this->requestFactory->createRequest('GET', '/spectacles/spec-5/soiree');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, ['id' => 'spec-5']);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['soiree']['id'])->toBe('soiree-10');
    expect($body['links']['self']['href'])->toBe('/spectacles/spec-5/soiree');
    expect($body['links']['soiree']['href'])->toBe('/soirees/soiree-10');
});

it('lève une HttpNotFoundException si le spectacle n\'a pas de soirée associée', function () {
    $this->serviceSpectacleMock
        ->method('getSoireeDetailBySpectacleId')
        ->with('spec-invalide')
        ->willThrowException(new DataNotFoundException('Soirée introuvable'));

    $action = new GetSoireeBySpectacleIdAction($this->serviceSpectacleMock);

    $request = $this->requestFactory->createRequest('GET', '/spectacles/spec-invalide/soiree');
    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['id' => 'spec-invalide']);
})->throws(HttpNotFoundException::class, 'Soirée introuvable');