<?php

use nrv\adapters\controllers\actions\GetMyBilletsAction;
use nrv\application\dto\BilletDTO;
use nrv\application\ports\api\ServiceBilletInterface;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceBilletMock = $this->createMock(ServiceBilletInterface::class);
});

it('retourne la liste des billets de l\'utilisateur au format JSON', function () {
    $billetDTO = $this->createMock(BilletDTO::class);
    $billetDTO->reference = 'BILLET-999';
    $billetDTO->nomAcheteur = 'Dupont Jean';
    $billetDTO->categorieTarif = 'Soirée';
    $billetDTO->prixPaye = 25.00;
    $billetDTO->estPass = false;
    $billetDTO->token = 'tok-999';
    $billetDTO->soirees = [
        ['id' => 'soiree-10', 'nom' => 'Soirée Electro']
    ];

    $this->serviceBilletMock
        ->expects($this->once())
        ->method('getBilletsByUserId')
        ->with('user-123')
        ->willReturn([$billetDTO]);

    $action = new GetMyBilletsAction($this->serviceBilletMock);

    $request = $this->requestFactory->createRequest('GET', '/mes-billets')
        ->withAttribute('idUtilisateur', 'user-123');

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['billets'])->toHaveCount(1);
    expect($body['billets'][0]['billet']['reference'])->toBe('BILLET-999');
    expect($body['billets'][0]['billet']['soirees'][0]['links']['soiree']['href'])->toBe('/soirees/soiree-10');
    expect($body['billets'][0]['links']['self']['href'])->toBe('/billets/tok-999');
});