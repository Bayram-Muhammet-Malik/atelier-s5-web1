<?php

use nrv\adapters\controllers\actions\GetMinMaxAction;
use nrv\application\dto\MinMaxDateDTO;
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

it('renvoie les dates min et max au format JSON', function () {
    $minMaxDTO = $this->createMock(MinMaxDateDTO::class);
    $minMaxDTO->minDate = '2027-06-01';
    $minMaxDTO->maxDate = '2027-06-15';

    $this->serviceSoireeMock
        ->expects($this->once())
        ->method('getMinMaxDate')
        ->willReturn($minMaxDTO);

    $action = new GetMinMaxAction($this->serviceSoireeMock);

    $request = $this->requestFactory->createRequest('GET', '/soirees/minmax');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['date_limits']['min'])->toBe('2027-06-01');
    expect($body['date_limits']['max'])->toBe('2027-06-15');
});

it('lève une HttpNotFoundException si le service lève DataNotFoundException', function () {
    $this->serviceSoireeMock
        ->method('getMinMaxDate')
        ->willThrowException(new DataNotFoundException('Aucune soirée disponible'));

    $action = new GetMinMaxAction($this->serviceSoireeMock);

    $request = $this->requestFactory->createRequest('GET', '/soirees/minmax');
    $response = $this->responseFactory->createResponse();

    $action($request, $response, []);
})->throws(HttpNotFoundException::class, 'Aucune soirée disponible');