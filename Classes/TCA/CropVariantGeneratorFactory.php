<?php

declare(strict_types=1);

namespace Helhum\TopImage\TCA;

class CropVariantGeneratorFactory
{
    /**
     * @var ImageVariantConfigurationInterface[]
     */
    private readonly array $imageVariantConfiguration;

    public function __construct(ImageVariantConfigurationInterface ...$imageVariantConfigurations)
    {
        $this->imageVariantConfiguration = $imageVariantConfigurations;
    }

    public function createGenerator(): CropVariantGenerator
    {
        $imageVariants = [];
        foreach ($this->imageVariantConfiguration as $configuration) {
            $imageVariants[] = $configuration->getImageVariantDefinitions();
        }

        return new CropVariantGenerator(array_merge([], ...$imageVariants));
    }
}
