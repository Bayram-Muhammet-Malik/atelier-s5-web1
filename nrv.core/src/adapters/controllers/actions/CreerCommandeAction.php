<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceCommandeInterface;

class CreerCommandeAction extends AbstractAction {
    private ServiceCommandeInterface $serviceCommande;

    public function __construct(ServiceCommandeInterface $serviceCommande) {
        $this->serviceCommande = $serviceCommande;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $idUtilisateur = $rq->getAttribute('idUtilisateur');

        $corps = (array) $rq->getParsedBody();
        $lignes = $corps['lignes'] ?? [];
        if (!is_array($lignes)) {
            throw new HttpBadRequestException($rq, "Le champ « lignes » doit être une liste.");
        }

        try {
            $commande = $this->serviceCommande->creerCommande($idUtilisateur, $lignes);
        } catch (ValidationException $e) {
            throw new HttpBadRequestException($rq, implode(' ', $e->getErrors()));
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        }

        return $this->json($rs, [
            'commande' => $commande,
            'links' => [
                'paiement' => ['href' => '/commandes/' . $commande->id . '/paiement'],
            ],
        ], 201);
    }
}
