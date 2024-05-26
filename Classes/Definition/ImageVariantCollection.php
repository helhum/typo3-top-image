<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

use Ds\Map;
use Helhum\TopImage\TCA\ImageVariantConfigurationInterface;

class ImageVariantCollection
{
    /**
     * @var Map<non-empty-string, ImageVariant>
     */
    private readonly Map $imageVariants;

    public function __construct(ImageVariantConfigurationInterface ...$imageVariantConfigurations)
    {
        $this->imageVariants = new Map();
        foreach ($imageVariantConfigurations as $configuration) {
            foreach ($configuration->getImageVariantDefinitions() as $imageVariantDefinition) {
                $this->imageVariants->put($imageVariantDefinition->id, $imageVariantDefinition);
            }
        }
    }

    public function get(string $id): ImageVariant
    {
        if ($id === '') {
            throw new \OutOfBoundsException('$id argument is expected to be a non empty string', 1716756314);
        }
        return $this->imageVariants->get($id);
    }

    /**
     * @return array<non-empty-string, ImageVariant>
     */
    public function asArray(): array
    {
        return $this->imageVariants->toArray();
    }
}
