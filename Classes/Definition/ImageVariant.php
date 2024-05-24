<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ImageVariant
{
    /**
     * @param CropVariant[] $cropVariants
     */
    public function __construct(
        public readonly string $id,
        public readonly ContentField $appliesTo,
        public readonly ?array $cropVariants = null,
    ) {
    }
}
