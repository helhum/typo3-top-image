<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ImageVariant
{
    /**
     * @param ImageSource[] $sources
     * @param CropVariant[] $cropVariants
     */
    public function __construct(
        public readonly string $id,
        public readonly ContentField $appliesTo,
        public readonly ?array $sources = null,
        public readonly ?array $cropVariants = null,
    ) {
    }
}
