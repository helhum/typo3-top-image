<?php

declare(strict_types=1);

namespace Helhum\TopImage\TCA;

use Helhum\TopImage\Definition\ImageVariant;

interface ImageVariantConfigurationInterface
{
    /**
     * @return ImageVariant[]
     */
    public function getImageVariantDefinitions(): array;
}
