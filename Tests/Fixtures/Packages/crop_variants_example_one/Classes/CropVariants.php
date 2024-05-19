<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Fixture\ExampleOne;

use Helhum\TopImage\Definition;
use Helhum\TopImage\TCA\ImageManipulationConfigurationInterface;

class CropVariants implements ImageManipulationConfigurationInterface
{
    public function getImageManipulationDefinitions(): array
    {
        return [
            new Definition\ImageManipulation(
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'test',
                        title: 'Test Label',
                        allowedAspectRatios: [
                            new Definition\FreeRatio()
                        ],
                    ),
                ],
                table: 'example_table',
                field: 'example_field',
                type: '0',
            )
        ];
    }
}
