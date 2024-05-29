<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Functional\Listener;

use Helhum\TopImage\Definition\TCA;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class CropVariantTcaListenerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'helhum/typo3-top-image',
        'helhum/typo3-top-image-fixture-example-one',
    ];

    #[Test]
    public function testExtensionsAreLoadedAsExpected(): void
    {
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        self::assertTrue($packageManager->isPackageActive('top_image'));
        self::assertTrue($packageManager->isPackageActive('top_image_example_one'));
    }

    #[Test]
    public function tcaForExampleIsProperlyExposed(): void
    {
        $actualTca = new TCA(null);
        self::assertSame('Test Label', $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.title'));
        self::assertSame(0.23, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.coverAreas.0.x'));
        self::assertSame(0.42, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.coverAreas.0.y'));
        self::assertSame(0.19, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.coverAreas.0.width'));
        self::assertSame(0.48, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.coverAreas.0.height'));
        self::assertSame(0.23, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.focusArea.x'));
        self::assertSame(0.42, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.focusArea.y'));
        self::assertSame(0.19, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.focusArea.width'));
        self::assertSame(0.48, $actualTca->get('example_table.types.0.columnsOverrides.example_field.config.overrideChildTca.columns.crop.config.cropVariants.test.focusArea.height'));
    }

    #[Test]
    public function tcaForOtherExampleIsProperlyExposed(): void
    {
        $actualTca = new TCA(null);
        self::assertSame('Other Test Label', $actualTca->get('other_example_table.types.one.columnsOverrides.other_example_field.config.overrideChildTca.columns.crop.config.cropVariants.other_test.title'));
        self::assertSame('Other Test Label', $actualTca->get('other_example_table.types.two.columnsOverrides.other_example_field.config.overrideChildTca.columns.crop.config.cropVariants.other_test.title'));
    }
}
