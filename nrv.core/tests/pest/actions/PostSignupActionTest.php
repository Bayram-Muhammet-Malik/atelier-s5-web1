<?php

use nrv\adapters\controllers\actions\PostSignupAction;
use nrv\application\ports\api\ServiceUserInterface;
use nrv\domain\entities\Utilisateur;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceUserMock = $this->createMock(ServiceUserInterface::class);
});

it('inscrit un nouvel utilisateur et renvoie un statut 201 avec le token JWT', function () {
    $userMock = $this->createMock(Utilisateur::class);

    $this->serviceUserMock
        ->expects($this->once())
        ->method('register')
        ->with('Dupont', 'Jean', 'jean@example.com', 'password123')
        ->willReturn($userMock);

    $this->serviceUserMock
        ->expects($this->once())
        ->method('generateJwt')
        ->with($userMock)
        ->willReturn('jwt.fake.token.signup');

    $action = new PostSignupAction($this->serviceUserMock);

    $request = $this->requestFactory->createRequest('POST', '/signup')
        ->withParsedBody([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean@example.com',
            'mdp' => 'password123'
        ]);

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response);

    expect($res->getStatusCode())->toBe(201);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['token'])->toBe('jwt.fake.token.signup');
});

it('renvoie une erreur JSON avec un statut 400 si l\'inscription échoue', function () {
    $this->serviceUserMock
        ->method('register')
        ->willThrowException(new \Exception('Cet email est déjà utilisé.'));

    $action = new PostSignupAction($this->serviceUserMock);

    $request = $this->requestFactory->createRequest('POST', '/signup')
        ->withParsedBody([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'existant@example.com',
            'mdp' => 'password123'
        ]);

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response);

    expect($res->getStatusCode())->toBe(400);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['error'])->toBe('Cet email est déjà utilisé.');
});