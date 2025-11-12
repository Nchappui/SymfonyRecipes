<?php

namespace App\DTO;

class CategoryWithCountDTO
{

    public function __construct(
        public int $id,
        public string $name,
        public int $count
    ) {}
}
