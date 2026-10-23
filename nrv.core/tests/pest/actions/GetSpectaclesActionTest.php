<?php

use nrv\adapters\controllers\actions\GetSpectaclesAction;
use nrv\application\dto\SpectacleDTO;
use nrv\application\ports\api\ServiceSpectacleInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceSpectacleMock = $this->createMock(ServiceSpectacleInterface::class);
});

it('retourne les spectacles filtrés et gère la pagination', function () {
    $spec1 = $this->createMock(SpectacleDTO::class);
    $spec1->id = 'spec-1';

    $spec2 = $this->createMock(SpectacleDTO::class);
    $spec2->id = 'spec-2';

    $this->serviceSpectacleMock
        ->expects($this->once())
        ->method('getListeSpectacles')
        ->with('Rock', '2027-06-10', 2, 2)
        ->willReturn([$spec1, $spec2]);

    $action = new GetSpectaclesAction($this->serviceSpectacleMock);

    $request = $this->requestFactory->createRequest('GET', '/spectacles?style=Rock&date=2027-06-10&limit=2&offset=2');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['spectacles'])->toHaveCount(2);
    expect($body['spectacles'][0]['links']['soiree']['href'])->toBe('/spectacles/spec-1/soiree');
    expect($body['links']['prev']['href'])->toContain('offset=0');
    expect($body['links']['next']['href'])->toContain('offset=4');
});

it('lève une HttpBadRequestException si le paramètre style n\'est pas une chaîne', function () {
    $action = new GetSpectaclesAction($this->serviceSpectacleMock);

    $request = $this->requestFactory->createRequest('GET', '/spectacles')
        ->withQueryParams(['style' => ['invalide']]);
    $response = $this->responseFactory->createResponse();

    $action($request, $response, []);
})->throws(HttpBadRequestException::class, 'Le paramètre style est invalide.');