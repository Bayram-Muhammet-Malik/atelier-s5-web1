<?php
declare(strict_types=1);

namespace nrv\application\dto;

class MinMaxDateDTO {
    public string $minDate;
    public string $maxDate;

    public function __construct(string $min, string $max) {
        $this->minDate = $min;
        $this->maxDate = $max;
    }
}