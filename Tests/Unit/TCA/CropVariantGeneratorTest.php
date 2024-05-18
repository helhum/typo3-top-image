<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Unit\TCA;

use Helhum\TopImage\Definition\CropVariant;
use Helhum\TopImage\Definition\ImageManipulation;
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
                new ImageManipulation(
                    [
                        new CropVariant(
                            'test',
                            'Test Label',
                        )
                    ],
                    'example_table',
                    'example_field',
                )
            ]
        );
        $tca = [
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
        ];
        $result = $generator->createImageManipulationOverrides($tca);
        $expectedTca = array_replace_recursive(
            $tca,
            [
                'example_table' => [
                    'types' => [
                        0 => [
                            'columnsOverrides' => [
                                'example_field' => [
                                    'config' => [
                                        'overrideChildTca' => [
                                            'columns' => [
                                                'crop' => [
                                                    'config' => [
                                                        'cropVariants' => [
                                                            'test' => [
                                                                'allowedAspectRatios' => [
                                                                    'NaN' => [
                                                                        'title' => 'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
                                                                        'value' => 0.0,
                                                                    ],
                                                                ],
                                                                'title' => 'Test Label',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        );

        self::assertEquals($expectedTca, $result);
    }
}
