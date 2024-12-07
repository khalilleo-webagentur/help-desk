<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Dto;

final readonly class Search
{
    public function __construct(
        public bool   $searchable,
        public string $path,
        public string $backTo,
    ) {
    }
}