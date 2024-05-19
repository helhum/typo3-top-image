<?php

declare(strict_types=1);

namespace Helhum\TopImage\TCA;

class CropVariantGeneratorFactory
{
    /**
     * @var ImageManipulationConfigurationInterface[]
     */
    private readonly array $imageManipulationDefinitions;

    public function __construct(ImageManipulationConfigurationInterface ...$imageManipulationDefinitions)
    {
        $this->imageManipulationDefinitions = $imageManipulationDefinitions;
    }

    public function createGenerator(): CropVariantGenerator
    {
        $collectedDefinitions = [];
        foreach ($this->imageManipulationDefinitions as $definitions) {
            $collectedDefinitions[] = $definitions->getImageManipulationDefinitions();
        }

        return new CropVariantGenerator(array_merge([], ...$collectedDefinitions));
    }
}
