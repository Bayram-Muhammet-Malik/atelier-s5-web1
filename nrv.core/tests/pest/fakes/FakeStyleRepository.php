<?php
declare(strict_types=1);

namespace tests\pest\fakes;

use nrv\application\ports\spi\StyleRepositoryInterface;

// Faux repository des styles : renvoie la liste qu'on lui donne
class FakeStyleRepository implements StyleRepositoryInterface {

    public function __construct(
        private array $styles
    ) {}

    public function findAll(): array {
        return $this->styles;
    }
}
