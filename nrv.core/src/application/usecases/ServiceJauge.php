<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\ports\api\ServiceJaugeInterface;
use nrv\application\ports\spi\JaugeRepositoryInterface;

class ServiceJauge implements ServiceJaugeInterface {

    private JaugeRepositoryInterface $jaugeRepository;

    public function __construct(JaugeRepositoryInterface $jaugeRepository) {
        $this->jaugeRepository = $jaugeRepository;
    }

    public function afficherJauge(): array {
        return $this->jaugeRepository->getJaugeSoirees();
    }
}
