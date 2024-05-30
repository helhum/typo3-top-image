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
            (new Definition\ImageVariant(
                id: 'other-example',
                appliesTo: [
                    new Definition\ContentField(
                        table: 'other_example_table',
                        field: 'other_example_field',
                    ),
                    new Definition\ContentField(
                        table: 'tt_content',
                        field: 'image',
                        type: 'image',
                    ),
                ],
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'other-test',
                        title: 'Other Test Label',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ))->applyTo(
                [
                    new Definition\ContentField(
                        table: 'tt_content',
                        field: 'media',
                        type: 'textmedia',
                    )
                ]
            ),
            new Definition\ImageVariant(
                id: 'content',
                appliesTo: [
                    new Definition\ContentField(
                        table: 'tt_content',
                        field: 'image',
                        type: 'image',
                    )
                ],
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'image-test',
                        title: 'Image Test Label',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ),
            new Definition\ImageVariant(
                id: 'other-content',
                appliesTo: [
                    new Definition\ContentField(
                        table: 'tt_content',
                        field: 'image',
                        type: 'image',
                    )
                ],
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'duplicate-variant',
                        title: 'Duplicate Variant Test Label',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ),
            new Definition\ImageVariant(
                id: 'yet-another-content',
                appliesTo: [
                    new Definition\ContentField(
                        table: 'tt_content',
                        field: 'image',
                        type: 'image',
                    )
                ],
                cropVariants: [
                    new Definition\CropVariant(
                        id: 'duplicate-variant',
                        title: 'Duplicate Variant Test Label',
                        allowedAspectRatios: [
                            new Definition\CropVariant\FreeRatio()
                        ],
                    ),
                ],
            ),
        ];
    }
}
