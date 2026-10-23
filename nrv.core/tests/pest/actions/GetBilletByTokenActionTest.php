<?php

use nrv\adapters\controllers\actions\GetBilletByTokenAction;
use nrv\application\dto\BilletDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceBilletInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceBilletMock = $this->createMock(ServiceBilletInterface::class);
});

it('retourne les informations du billet au format JSON avec un statut 200', function () {
    // 1. Mock du DTO du billet retourné par le service
    $billetDTO = $this->createMock(BilletDTO::class);
    $billetDTO->reference = 'BILLET-123';
    $billetDTO->nomAcheteur = 'Dupont Jean';
    $billetDTO->categorieTarif = 'Plein Tarif';
    $billetDTO->prixPaye = 30.00;
    $billetDTO->estPass = false;
    $billetDTO->soirees = [
        ['id' => 'soiree-1', 'nom' => 'Soirée Opening']
    ];

    $this->serviceBilletMock
        ->expects($this->once())
        ->method('afficherBillet')
        ->with('tok-12345')
        ->willReturn($billetDTO);

    $action = new GetBilletByTokenAction($this->serviceBilletMock);

    $request = $this->requestFactory->createRequest('GET', '/billets/tok-12345');
    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, ['token' => 'tok-12345']);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['billet']['reference'])->toBe('BILLET-123');
    expect($body['billet']['nomAcheteur'])->toBe('Dupont Jean');
    expect($body['billet']['soirees'][0]['links']['soiree']['href'])->toBe('/soirees/soiree-1');
    expect($body['links']['self']['href'])->toBe('/billets/tok-12345');
});

it('lève une HttpNotFoundException si le token est introuvable', function () {
    $this->serviceBilletMock
        ->method('afficherBillet')
        ->with('tok-invalide')
        ->willThrowException(new DataNotFoundException('Billet introuvable'));

    $action = new GetBilletByTokenAction($this->serviceBilletMock);

    $request = $this->requestFactory->createRequest('GET', '/billets/tok-invalide');
    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['token' => 'tok-invalide']);
})->throws(HttpNotFoundException::class, 'Billet introuvable');