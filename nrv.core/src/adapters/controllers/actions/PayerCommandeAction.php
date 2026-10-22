<?php
declare(strict_types=1);

namespace nrv\adapters\controllers\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use nrv\application\exceptions\AccesRefuseException;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\exceptions\ValidationException;
use nrv\application\ports\api\ServiceCommandeInterface;
use nrv\domain\exceptions\PlacesInsuffisantesException;
use nrv\domain\exceptions\StatusChangeNotAllowedException;

class PayerCommandeAction extends AbstractAction {
    private ServiceCommandeInterface $serviceCommande;

    public function __construct(ServiceCommandeInterface $serviceCommande) {
        $this->serviceCommande = $serviceCommande;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $idUtilisateur = $rq->getAttribute('idUtilisateur');
        $carte = (array) $rq->getParsedBody();

        try {
            $commande = $this->serviceCommande->payerCommande($args['id'], $idUtilisateur, $carte);
        } catch (ValidationException $e) {
            throw new HttpBadRequestException($rq, implode(' ', $e->getErrors()));
        } catch (DataNotFoundException $e) {
            throw new HttpNotFoundException($rq, $e->getMessage());
        } catch (AccesRefuseException $e) {
            throw new HttpForbiddenException($rq, $e->getMessage());
        } catch (StatusChangeNotAllowedException | PlacesInsuffisantesException $e) {
            throw new HttpException($rq, $e->getMessage(), 409);
        }

        $billets = [];
        foreach ($commande->billets as $billet) {
            $billets[] = ['href' => '/billets/' . $billet['token']];
        }

        return $this->json($rs, [
            'commande' => $commande,
            'links' => [
                'billets' => $billets,
            ],
        ]);
    }
}
