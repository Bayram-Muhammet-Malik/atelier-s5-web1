<?php
declare(strict_types=1);

namespace nrv\application\dto;

use nrv\domain\entities\Style;

class StyleDTO {
    public string $id;
    public string $libelle;

    public function __construct(string $id, string $libelle) {
        $this->id = $id;
        $this->libelle = $libelle;
    }
    
    public static function fromStyle(Style $style): StyleDTO {
        return new StyleDTO(
            $style->getId(),
            $style->getLibelle()
        );
    }
}
