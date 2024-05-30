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
                appliesTo: [
                    new Definition\ContentField(
                        table: 'example_table',
                        field: 'example_field',
                        type: '0',
                    )
                ],
                sources: [
                    new Definition\ImageSource(
                        widths: [
                            500,
                        ],
                        artDirection: new Definition\ImageSource\ArtDirection(
                            'test',
                        ),
                    ),
                ],
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'test',
                        title: 'Test Label',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                        focusArea: new Definition\CropVariant\Area(
                            width: 0.19,
                            height: 0.48,
                            offsetLeft: 0.23,
                            offsetTop: 0.42,
                        ),
                        coverAreas: [
                            new Definition\CropVariant\Area(
                                width: 0.19,
                                height: 0.48,
                                offsetLeft: 0.23,
                                offsetTop: 0.42,
                            ),
                        ],
                    ),
                ],
            )
        ];
    }
}
