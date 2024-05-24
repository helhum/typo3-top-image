<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition\CropVariant;

class Ratio
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly float $value,
    ) {
    }
}
