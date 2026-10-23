<?php

use nrv\adapters\controllers\actions\GetStylesAction;
use nrv\application\dto\StyleDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceStyleInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceStyleMock = $this->createMock(ServiceStyleInterface::class);
});

it('retourne la liste des styles au format JSON', function () {
    $style1 = $this->createMock(StyleDTO::class);
    $style1->libelle = 'Rock & Roll';

    $style2 = $this->createMock(StyleDTO::class);
    $style2->libelle = 'Electro';

    $this->serviceStyleMock
        ->expects($this->once())
        ->method('getStyles')
        ->willReturn([$style1, $style2]);

    $action = new GetStylesAction($this->serviceStyleMock);

    $request = $this->requestFactory->createRequest('GET', '/styles');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['styles'])->toHaveCount(2);
    expect($body['styles'][0]['links']['spectacles']['href'])->toBe('/spectacles?style=Rock+%26+Roll');
    expect($body['links']['self']['href'])->toBe('/styles');
});

it('lève une HttpNotFoundException si aucun style n\'est trouvé', function () {
    $this->serviceStyleMock
        ->method('getStyles')
        ->willThrowException(new DataNotFoundException('Aucun style trouvé'));

    $action = new GetStylesAction($this->serviceStyleMock);

    $request = $this->requestFactory->createRequest('GET', '/styles');
    $response = $this->responseFactory->createResponse();

    $action($request, $response, []);
})->throws(HttpNotFoundException::class, 'Aucun style trouvé');