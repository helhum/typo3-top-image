<?php

declare(strict_types=1);

namespace Helhum\TopImage\TCA;

use Helhum\TopImage\Definition\ImageVariant;
use Helhum\TopImage\Definition\ImageVariantCollection;

class CropVariantGeneratorFactory
{
    /**
     * @var ImageVariant[]
     */
    private readonly array $imageVariants;

    public function __construct(ImageVariantCollection $imageVariantCollection)
    {
        $this->imageVariants = $imageVariantCollection->imageVariants->toArray();
    }

    public function createGenerator(): CropVariantGenerator
    {
        return new CropVariantGenerator($this->imageVariants);
    }
}
