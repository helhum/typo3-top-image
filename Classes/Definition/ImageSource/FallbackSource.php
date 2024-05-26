<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition\ImageSource;

class FallbackSource
{
    public function __construct(
        public readonly int $width,
        public readonly ?string $cropVariant = null,
    ) {
    }
}
