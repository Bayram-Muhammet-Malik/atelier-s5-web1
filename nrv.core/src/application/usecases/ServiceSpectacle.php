<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\ports\api\ServiceSpectacleInterface;
use nrv\application\ports\spi\SoireeRepositoryInterface;
use nrv\application\ports\spi\TarifRepositoryInterface;
use nrv\application\ports\spi\SpectacleRepositoryInterface;
use nrv\application\dto\SoireeDetailDTO;
use nrv\application\dto\SpectacleDTO;
use nrv\application\exceptions\DataNotFoundException;

class ServiceSpectacle implements ServiceSpectacleInterface
{
    private SoireeRepositoryInterface $soireeRepository;
    private TarifRepositoryInterface $tarifRepository;
    private SpectacleRepositoryInterface $spectacleRepository;

    public function __construct(
        SoireeRepositoryInterface $soireeRepository,
        TarifRepositoryInterface $tarifRepository,
        SpectacleRepositoryInterface $spectacleRepository
    ) {
        $this->soireeRepository = $soireeRepository;
        $this->tarifRepository = $tarifRepository;
        $this->spectacleRepository = $spectacleRepository;
    }

    public function getListeSpectacles(): array
    {
        $spectacles = $this->spectacleRepository->findAll();

        $spectaclesDTO = [];
        foreach ($spectacles as $spectacle) {
            $nomsArtistes = [];
            foreach ($spectacle->getArtistes() as $artiste) {
                $nomsArtistes[] = $artiste->getNom();
            }
            $artistesNoms = implode(', ', $nomsArtistes);

            $spectaclesDTO[] = new SpectacleDTO(
                $spectacle->getId(),
                $spectacle->getTitre(),
                $artistesNoms ?: 'Artiste inconnu',
                $spectacle->getDescription() ?? '',
                $spectacle->getStyle()->getLibelle(),
                $spectacle->getDateSoiree() ?? '',
                $spectacle->getHoraire() ?? '',
                $spectacle->getUrlVideo(),
            );
        }

        return $spectaclesDTO;
    }

    public function getSoireeDetailBySpectacleId(string $idSpectacle): SoireeDetailDTO
    {
        $soiree = $this->soireeRepository->getSoireeBySpectacleId($idSpectacle);

        if (!$soiree) {
            throw new DataNotFoundException("La soirée associée à ce spectacle n'existe pas.");
        }

        $tarifs = $this->tarifRepository->findAll();

        $tarifNormal = 0.0;
        $tarifReduit = 0.0;

        foreach ($tarifs as $tarif) {
            $libelle = strtolower($tarif->getLibelle());
            if ($libelle === 'normal') {
                $tarifNormal = $tarif->getPrix();
            } elseif ($libelle === 'reduit' || $libelle === 'réduit') {
                $tarifReduit = $tarif->getPrix();
            }
        }

        $spectaclesDTO = [];
        foreach ($soiree->getSpectacles() as $spectacle) {
            // Remplacement de array_map + fn par une boucle foreach classique
            $nomsArtistes = [];
            foreach ($spectacle->getArtistes() as $artiste) {
                $nomsArtistes[] = $artiste->getNom();
            }
            $artistesNoms = implode(', ', $nomsArtistes);

            $spectaclesDTO[] = new SpectacleDTO(
                $spectacle->getId(),
                $spectacle->getTitre(),
                $artistesNoms ?: 'Inconnu',
                $spectacle->getDescription() ?? '',
                $spectacle->getStyle()->getLibelle(),
                $spectacle->getHoraire() ?? '',
                $spectacle->getUrlVideo()
            );
        }

        return new SoireeDetailDTO(
            $soiree->getId(),
            $soiree->getNom(),
            $soiree->getThematique(),
            $soiree->getDateHeureDebut()->format('Y-m-d'),
            $soiree->getDateHeureDebut()->format('H:i:s'),
            $soiree->getLieu()->getNom(),
            $tarifNormal,
            $tarifReduit,
            $spectaclesDTO
        );
    }
}