<?php

use nrv\adapters\controllers\actions\PostLoginAction;
use nrv\application\ports\api\ServiceUserInterface;
use nrv\domain\entities\Utilisateur;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

beforeEach(function () {
    $this->requestFactory = new RequestFactory();
    $this->responseFactory = new ResponseFactory();
    $this->serviceUserMock = $this->createMock(ServiceUserInterface::class);
});

it('connecte l\'utilisateur et renvoie un token JWT avec un statut 200', function () {
    $userMock = $this->createMock(Utilisateur::class);

    $this->serviceUserMock
        ->expects($this->once())
        ->method('login')
        ->with('test@example.com', 'secret123')
        ->willReturn($userMock);

    $this->serviceUserMock
        ->expects($this->once())
        ->method('generateJwt')
        ->with($userMock)
        ->willReturn('jwt.fake.token');

    $action = new PostLoginAction($this->serviceUserMock);

    $request = $this->requestFactory->createRequest('POST', '/login')
        ->withParsedBody([
            'email' => 'test@example.com',
            'mdp' => 'secret123'
        ]);

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response);

    expect($res->getStatusCode())->toBe(200);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['token'])->toBe('jwt.fake.token');
});

it('renvoie une erreur JSON avec un statut 401 en cas de mauvais identifiants', function () {
    $this->serviceUserMock
        ->method('login')
        ->willThrowException(new \Exception('Identifiants invalides'));

    $action = new PostLoginAction($this->serviceUserMock);

    $request = $this->requestFactory->createRequest('POST', '/login')
        ->withParsedBody([
            'email' => 'test@example.com',
            'mdp' => 'mauvais-mdp'
        ]);

    $response = $this->responseFactory->createResponse();

    $res = $action($request, $response);

    expect($res->getStatusCode())->toBe(401);

    $body = json_decode((string) $res->getBody(), true);
    expect($body['error'])->toBe('Identifiants invalides');
});