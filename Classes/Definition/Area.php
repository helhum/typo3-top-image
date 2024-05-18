<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class Area
{
    public function __construct(
        public readonly float $width,
        public readonly float $height,
        public readonly float $offsetLeft = 0.0,
        public readonly float $offsetTop = 0.0,
    ) {
    }
}
