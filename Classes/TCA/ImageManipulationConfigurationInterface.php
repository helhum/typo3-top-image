<?php

declare(strict_types=1);

namespace Helhum\TopImage\TCA;

use Helhum\TopImage\Definition\ImageManipulation;

interface ImageManipulationConfigurationInterface
{
    /**
     * @return ImageManipulation[]
     */
    public function getImageManipulationDefinitions(): array;
}
