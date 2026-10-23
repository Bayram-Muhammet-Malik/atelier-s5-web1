<?php

use nrv\adapters\controllers\actions\PayerCommandeAction;
use nrv\application\dto\CommandeDTO;
use nrv\application\exceptions\AccesRefuseException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceCommandeInterface;
use nrv\domain\exceptions\PlacesInsuffisantesException;
use nrv\domain\exceptions\StatusChangeNotAllowedException;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceCommandeMock = $this->createMock(ServiceCommandeInterface::class);
});

it('paye une commande avec succès et renvoie un statut 200 avec les liens des billets', function () {
    $commandeDTO = $this->createMock(CommandeDTO::class);
    $commandeDTO->billets = [
        ['token' => 'tok-billet-1'],
        ['token' => 'tok-billet-2']
    ];

    $carteData = ['numero' => '1234567890123456', 'cvv' => '123'];

    $this->serviceCommandeMock
        ->expects($this->once())
        ->method('payerCommande')
        ->with('cmd-100', 'user-42', $carteData)
        ->willReturn($commandeDTO);

    $action = new PayerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes/cmd-100/pay')
        ->withAttribute('idUtilisateur', 'user-42')
        ->withParsedBody($carteData);

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, ['id' => 'cmd-100']);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['links']['billets'])->toHaveCount(2);
    expect($body['links']['billets'][0]['href'])->toBe('/billets/tok-billet-1');
});

it('lève une HttpBadRequestException en cas de ValidationException', function () {
    $this->serviceCommandeMock
        ->method('payerCommande')
        ->willThrowException(new ValidationException(['Carte invalide.']));

    $action = new PayerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes/cmd-100/pay')
        ->withAttribute('idUtilisateur', 'user-42')
        ->withParsedBody([]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['id' => 'cmd-100']);
})->throws(HttpBadRequestException::class, 'Carte invalide.');

it('lève une HttpNotFoundException si la commande n\'existe pas', function () {
    $this->serviceCommandeMock
        ->method('payerCommande')
        ->willThrowException(new DataNotFoundException('Commande introuvable'));

    $action = new PayerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes/invalide/pay')
        ->withAttribute('idUtilisateur', 'user-42')
        ->withParsedBody([]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['id' => 'invalide']);
})->throws(HttpNotFoundException::class, 'Commande introuvable');

it('lève une HttpForbiddenException en cas d\'accès refusé', function () {
    $this->serviceCommandeMock
        ->method('payerCommande')
        ->willThrowException(new AccesRefuseException('Accès interdit'));

    $action = new PayerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes/cmd-100/pay')
        ->withAttribute('idUtilisateur', 'autre-user')
        ->withParsedBody([]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['id' => 'cmd-100']);
})->throws(HttpForbiddenException::class, 'Accès interdit');

it('lève une HttpException (409) en cas de conflit sur le statut ou les places', function () {
    $this->serviceCommandeMock
        ->method('payerCommande')
        ->willThrowException(new PlacesInsuffisantesException('Plus de places disponibles'));

    $action = new PayerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes/cmd-100/pay')
        ->withAttribute('idUtilisateur', 'user-42')
        ->withParsedBody([]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, ['id' => 'cmd-100']);
})->throws(HttpException::class, 'Plus de places disponibles');