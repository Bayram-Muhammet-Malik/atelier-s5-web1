<?php
declare(strict_types=1);

namespace nrv\domain\entities;

class Image {
    public function __construct(
        private string $id,
        private string $url
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getUrl(): string {
        return $this->url;
    }
}