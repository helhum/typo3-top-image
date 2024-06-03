<?php
declare(strict_types=1);

namespace Functional\Rendering;

use Helhum\TopImage\Definition\ImageSource;
use Helhum\TopImage\Rendering\ImgTag;
use Helhum\TopImage\Rendering\RenderedImage\Identifier;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ImgTagTest extends FunctionalTestCase
{
    private const cropVariants = '{
      "image_test": {
        "cropArea": {
          "height": 0.69453125,
          "width": 0.5947916666666667,
          "x": 0.059895833333333336,
          "y": 0
        },
        "selectedRatio": "NaN",
        "focusArea": null
      },
      "other_image": {
        "cropArea": {
          "x": 0.3729166666666667,
          "y": 0.36484375,
          "width": 0.6265625,
          "height": 0.634375
        },
        "selectedRatio": "NaN",
        "focusArea": null
      }
    }';
    protected array $testExtensionsToLoad = [
        'helhum/typo3-top-image',
        'helhum/typo3-top-image-fixture-example-one',
    ];

    protected array $configurationToUseInTestInstance = [
        'GFX' => [
            'processor_path' => '/opt/homebrew/bin/',
        ]
    ];

    #[Test]
    public function testExtensionsAreLoadedAsExpected(): void
    {
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        self::assertTrue($packageManager->isPackageActive('top_image'));
        self::assertTrue($packageManager->isPackageActive('top_image_example_one'));
    }

    #[Test]
    public function fetchingRenderedImagesFromResultReturnsProcessedFileOfCorrectSize(): void
    {
        $fileReference = $this->createFileReference();
        $fallbackSource = new ImageSource\FallbackSource(
            width: 300,
        );
        $imgTag = new ImgTag(
            source: $fallbackSource,
            fileReference: $fileReference,
        );
        $builtTag = $imgTag->build();
        $processedFile = $builtTag->renderedImages->get(Identifier::fromFallbackSource($fallbackSource));
        self::assertSame(300, $processedFile->getProperty('width'));
        self::assertSame(200, $processedFile->getProperty('height'));
    }

    #[Test]
    public function imgTagRenderedAsExpected(): void
    {
        $fileReference = $this->createFileReference();
        $imgTag = new ImgTag(
            source: new ImageSource\FallbackSource(
                width: 300,
            ),
            fileReference: $fileReference,
        );
        self::assertSame(sprintf('<img src="%s" width="300" height="200" />', $this->processExpectedFile($fileReference, 300)->getPublicUrl()), $imgTag->build()->render());
    }

    private function createFileReference(): FileReference
    {
        $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
        self::assertInstanceOf(ResourceStorage::class, $storage);
        $file = $storage->addFile(localFilePath: __DIR__ . '/../../Fixtures/Files/image1.jpg', targetFolder: $storage->getRootLevelFolder(), removeOriginal: false);
        self::assertInstanceOf(File::class, $file);
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_file_reference');
        $connection->insert(
            'sys_file_reference',
            [
                'uid' => 1,
                'uid_local' => $file->getUid(),
                'uid_foreign' => 1,
                'tablenames' => 'tt_content',
                'crop' => self::cropVariants,
            ]
        );
        return GeneralUtility::makeInstance(ResourceFactory::class)->getFileReferenceObject(1);
    }

    private function processExpectedFile(FileReference $fileReference, int $width, string $cropVariant = 'default'): ProcessedFile
    {
        $collection = CropVariantCollection::create(self::cropVariants);
        $cropArea = $collection->getCropArea($cropVariant);

        return $fileReference->getOriginalFile()->process(
            ProcessedFile::CONTEXT_IMAGECROPSCALEMASK,
            [
                'maxWidth' => $width,
                'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($fileReference->getOriginalFile()),
            ]
        );
    }
}
