<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ImageVariant
{
    /**
     * @param CropVariant[] $cropVariants
     */
    public function __construct(
        public readonly array $cropVariants,
        public readonly string $table,
        public readonly string $field,
        public readonly ?string $type = null,
    ) {
    }
}
