<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

use Ds\Map;
use Helhum\TopImage\TCA\ImageVariantConfigurationInterface;

class ImageVariantCollection
{
    /**
     * @var Map<string, ImageVariant>
     */
    public readonly Map $imageVariants;

    public function __construct(ImageVariantConfigurationInterface ...$imageVariantConfigurations)
    {
        $this->imageVariants = new Map();
        foreach ($imageVariantConfigurations as $configuration) {
            foreach ($configuration->getImageVariantDefinitions() as $imageVariantDefinition) {
                $this->imageVariants->put($imageVariantDefinition->id, $imageVariantDefinition);
            }
        }
    }
}
