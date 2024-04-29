<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class BasketItemDto
{
    public function __construct(
        public int $productId
    ) {
    }
}
