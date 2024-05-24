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
                appliesTo: new Definition\ContentField(
                    table: 'other_example_table',
                    field: 'other_example_field',
                ),
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'other_test',
                        title: 'Other Test Label',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ),
            new Definition\ImageVariant(
                id: 'content',
                appliesTo: new Definition\ContentField(
                    table: 'tt_content',
                    field: 'image',
                    type: 'image',
                ),
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'image_test',
                        title: 'Image Test',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ),
            new Definition\ImageVariant(
                id: 'other-content',
                appliesTo: new Definition\ContentField(
                    table: 'tt_content',
                    field: 'image',
                    type: 'image',
                ),
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'other_image',
                        title: 'Other Image Test',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ),
        ];
    }
}
