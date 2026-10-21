<?php
declare(strict_types=1);

namespace nrv\application\usecases;

use nrv\application\dto\StyleDTO;
use nrv\application\exceptions\DataNotFoundException;
use nrv\application\ports\api\ServiceStyleInterface;
use nrv\application\ports\spi\StyleRepositoryInterface;

class ServiceStyle implements ServiceStyleInterface {
    private StyleRepositoryInterface $styleRepository;

    public function __construct(
        StyleRepositoryInterface $styleRepository
    ) {
        $this->styleRepository = $styleRepository;
    }

    public function getStyles(): array {
        $styles = $this->styleRepository->findAll();
        $dtos = [];

        foreach ($styles as $style) $dtos[] = StyleDTO::fromStyle($style);

        return $dtos;
    }
}
