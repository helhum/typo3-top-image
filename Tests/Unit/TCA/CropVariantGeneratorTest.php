<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Unit\TCA;

use Helhum\TopImage\Definition\CropVariant;
use Helhum\TopImage\Definition\ImageVariant;
use Helhum\TopImage\Definition\TCA;
use Helhum\TopImage\TCA\CropVariantGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CropVariantGeneratorTest extends TestCase
{
    #[Test]
    public function generateForCustomTableWorks(): void
    {
        $generator = new CropVariantGenerator(
            [
                new ImageVariant(
                    id: 'test',
                    cropVariants: [
                        new CropVariant(
                            'test',
                            'Test Label',
                        )
                    ],
                    table: 'example_table',
                    field: 'example_field',
                )
            ]
        );
        $tca = new TCA([
            'example_table' => [
                'columns' => [
                    'example_field' => [

                    ],
                ],
                'types' => [
                    0 => [
                        'showitem' => 'example_field',
                    ],
                ],
            ],
        ]);
        $result = $generator->createTca($tca);
        $cropVariantConfig = [
            'test' => [
                'allowedAspectRatios' => [
                    'NaN' => [
                        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                        'value' => 0.0,
                    ],
                ],
                'title' => 'Test Label',
            ],
        ];
        $expectedTca = $tca->set(
            'example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants',
            $cropVariantConfig
        );

        self::assertSame($expectedTca->get(), $result->get());
    }

    #[Test]
    public function generateForCustomTableSetsConfigForConfiguredType(): void
    {
        $generator = new CropVariantGenerator(
            [
                new ImageVariant(
                    id: 'test',
                    cropVariants: [
                        new CropVariant(
                            'test',
                            'Test Label',
                        )
                    ],
                    table: 'example_table',
                    field: 'example_field',
                    type: '0',
                )
            ]
        );
        $tca = new TCA([
            'example_table' => [
                'columns' => [
                    'example_field' => [

                    ],
                ],
                'types' => [
                    0 => [
                        'showitem' => 'example_field',
                    ],
                    1 => [
                        'showitem' => 'example_field',
                    ],
                ],
            ],
        ]);
        $result = $generator->createTca($tca);
        $cropVariantConfig = [
            'test' => [
                'allowedAspectRatios' => [
                    'NaN' => [
                        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                        'value' => 0.0,
                    ],
                ],
                'title' => 'Test Label',
            ],
        ];
        $expectedTca = $tca->set(
            'example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants',
            $cropVariantConfig
        );

        self::assertSame($expectedTca->get(), $result->get());
        self::assertNull($expectedTca->get('example_table.types.1.columnsOverrides', null));
    }

    #[Test]
    public function generateForCustomTableSetsConfigForAllTypes(): void
    {
        $generator = new CropVariantGenerator(
            [
                new ImageVariant(
                    id: 'test',
                    cropVariants: [
                        new CropVariant(
                            'test',
                            'Test Label',
                        )
                    ],
                    table: 'example_table',
                    field: 'example_field',
                )
            ]
        );
        $tca = new TCA([
            'example_table' => [
                'columns' => [
                    'example_field' => [

                    ],
                ],
                'types' => [
                    0 => [
                        'showitem' => 'example_field',
                    ],
                    1 => [
                        'showitem' => 'example_field',
                    ],
                ],
            ],
        ]);
        $result = $generator->createTca($tca);
        $cropVariantConfig = [
            'test' => [
                'allowedAspectRatios' => [
                    'NaN' => [
                        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                        'value' => 0.0,
                    ],
                ],
                'title' => 'Test Label',
            ],
        ];
        $expectedTca = $tca
            ->set(
                'example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants',
                $cropVariantConfig
            )
            ->set(
                'example_table.types.1.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants',
                $cropVariantConfig
            );

        self::assertSame($expectedTca->get(), $result->get());
    }
}
