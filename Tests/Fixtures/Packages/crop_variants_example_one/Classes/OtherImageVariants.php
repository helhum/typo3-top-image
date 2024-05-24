<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Fixture\ExampleOne;

use Helhum\TopImage\Definition;
use Helhum\TopImage\TCA\ImageVariantConfigurationInterface;

class OtherImageVariants implements ImageVariantConfigurationInterface
{
    public function getImageVariantDefinitions(): array
    {
        return [
            new Definition\ImageVariant(
                id: 'otherExample',
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'other_test',
                        title: 'Other Test Label',
                        allowedAspectRatios: [
                            new Definition\FreeRatio()
                        ],
                    ),
                ],
                table: 'other_example_table',
                field: 'other_example_field',
            )
        ];
    }
}
