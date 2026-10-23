<?php

use nrv\adapters\controllers\actions\CreerCommandeAction;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceCommandeInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceCommandeMock = Mockery::mock(ServiceCommandeInterface::class);
});

it('crée une commande avec succès et renvoie un statut 201', function () {
    // Objet commande factice à retourner par le service mocké
    $commandeFactice = (object) ['id' => 'cmd-12345'];

    $this->serviceCommandeMock
        ->shouldReceive('creerCommande')
        ->once()
        ->with('user-100', [['soiree_id' => 's1', 'quantite' => 2]])
        ->andReturn($commandeFactice);

    $action = new CreerCommandeAction($this->serviceCommandeMock);

    // Préparation de la requête avec l'attribut d'utilisateur et le corps de requête
    $request = $this->requestFactory->createRequest('POST', '/commandes')
        ->withAttribute('idUtilisateur', 'user-100')
        ->withParsedBody([
            'lignes' => [['soiree_id' => 's1', 'quantite' => 2]]
        ]);

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response, []);

    expect($res->getStatusCode())->toBe(201);
    
    $body = json_decode((string) $res->getBody(), true);
    expect($body['commande']['id'])->toBe('cmd-12345');
    expect($body['links']['paiement']['href'])->toBe('/commandes/cmd-12345/paiement');
});

it('lève une HttpBadRequestException si "lignes" n\'est pas un tableau', function () {
    $action = new CreerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes')
        ->withAttribute('idUtilisateur', 'user-100')
        ->withParsedBody([
            'lignes' => 'pas-un-tableau'
        ]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, []);
})->throws(HttpBadRequestException::class, "Le champ « lignes » doit être une liste.");

it('lève une HttpBadRequestException en cas de ValidationException du service', function () {
    $this->serviceCommandeMock
        ->shouldReceive('creerCommande')
        ->andThrow(new ValidationException(['Quantité invalide.']));

    $action = new CreerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes')
        ->withAttribute('idUtilisateur', 'user-100')
        ->withParsedBody(['lignes' => []]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, []);
})->throws(HttpBadRequestException::class, 'Quantité invalide.');

it('lève une HttpNotFoundException si le service lève une DataNotFoundException', function () {
    $this->serviceCommandeMock
        ->shouldReceive('creerCommande')
        ->andThrow(new DataNotFoundException('Soirée non trouvée'));

    $action = new CreerCommandeAction($this->serviceCommandeMock);

    $request = $this->requestFactory->createRequest('POST', '/commandes')
        ->withAttribute('idUtilisateur', 'user-100')
        ->withParsedBody(['lignes' => []]);

    $response = $this->responseFactory->createResponse();

    $action($request, $response, []);
})->throws(HttpNotFoundException::class, 'Soirée non trouvée');