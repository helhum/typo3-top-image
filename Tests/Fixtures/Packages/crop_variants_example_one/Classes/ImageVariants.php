<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Fixture\ExampleOne;

use Helhum\TopImage\Definition;
use Helhum\TopImage\TCA\ImageVariantConfigurationInterface;

class ImageVariants implements ImageVariantConfigurationInterface
{
    public function getImageVariantDefinitions(): array
    {
        return [
            new Definition\ImageVariant(
                id: 'example',
                appliesTo: new Definition\ContentField(
                    table: 'example_table',
                    field: 'example_field',
                    type: '0',
                ),
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'test',
                        title: 'Test Label',
                        allowedAspectRatios: [
                            new Definition\FreeRatio()
                        ],
                    ),
                ],
            )
        ];
    }
}
