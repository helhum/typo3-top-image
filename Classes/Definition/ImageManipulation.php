<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ImageManipulation
{
    /**
     * @param CropVariant[] $cropVariants
     * @param string|null $type
     */
    public function __construct(
        public readonly array $cropVariants,
        public readonly string $table,
        public readonly string $field,
        public readonly ?string $type = null,
    ) {
    }
}
