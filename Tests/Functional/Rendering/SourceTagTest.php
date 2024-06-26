<?php
declare(strict_types=1);

namespace Functional\Rendering;

use Helhum\TopImage\Definition\ImageSource;
use Helhum\TopImage\Rendering\RenderedImage\Identifier;
use Helhum\TopImage\Rendering\SourceTag;
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

class SourceTagTest extends FunctionalTestCase
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
        $source = new ImageSource(
            widths: [300, 600],
        );
        $sourceTag = new SourceTag(
            source: $source,
            fileReference: $fileReference,
        );
        $builtTag = $sourceTag->build();
        $processedFile = $builtTag->renderedImages->get(new Identifier(source: $source, width: 300));
        self::assertSame(300, $processedFile->getProperty('width'));
        self::assertSame(200, $processedFile->getProperty('height'));
        $processedFile = $builtTag->renderedImages->get(new Identifier(source: $source, width: 600));
        self::assertSame(600, $processedFile->getProperty('width'));
        self::assertSame(400, $processedFile->getProperty('height'));
    }

    #[Test]
    public function sourceFileWithSingleWidth(): void
    {
        $fileReference = $this->createFileReference();
        $sourceTag = new SourceTag(
            source: new ImageSource(
                widths: [300],
            ),
            fileReference: $fileReference,
        );
        self::assertSame(sprintf('<source srcset="%s 300w" width="300" height="200" />', $this->processExpectedFile($fileReference, 300)->getPublicUrl()), $sourceTag->build()->render());
    }

    #[Test]
    public function sourceFileWithMultipleWidthsAndSizes(): void
    {
        $fileReference = $this->createFileReference();
        $cropVariant = 'image_test';
        $sourceTag = new SourceTag(
            source: new ImageSource(
                widths: [1000, 300, 600],
                sizes: ['(min-width: 760px) 50vw', '100vw'],
                artDirection: new ImageSource\ArtDirection(
                    cropVariant: $cropVariant,
                    media: '(max-width: 2048px)'
                ),
            ),
            fileReference: $fileReference,
        );
        self::assertSame(
            sprintf(
                '<source srcset="%s 300w, %s 600w, %s 1000w" sizes="(min-width: 760px) 50vw, 100vw" media="(max-width: 2048px)" width="300" height="234" />',
                $this->processExpectedFile($fileReference, 300, $cropVariant)->getPublicUrl(),
                $this->processExpectedFile($fileReference, 600, $cropVariant)->getPublicUrl(),
                $this->processExpectedFile($fileReference, 1000, $cropVariant)->getPublicUrl(),
            ),
            $sourceTag->build()->render(),
        );
    }

    #[Test]
    public function sourceFileWithMultipleWidthsSomeTooLarge(): void
    {
        $fileReference = $this->createFileReference();
        $cropVariant = 'image_test';
        $sourceTag = new SourceTag(
            source: new ImageSource(
                widths: [1200, 600, 300],
                artDirection: new ImageSource\ArtDirection(
                    cropVariant: $cropVariant,
                    media: '(max-width: 2048px)'
                ),
            ),
            fileReference: $fileReference,
        );
        self::assertSame(
            sprintf(
                '<source srcset="%s 300w, %s 600w, %s 1142w" media="(max-width: 2048px)" width="300" height="234" />',
                $this->processExpectedFile($fileReference, 300, $cropVariant)->getPublicUrl(),
                $this->processExpectedFile($fileReference, 600, $cropVariant)->getPublicUrl(),
                $this->processExpectedFile($fileReference, 1200, $cropVariant)->getPublicUrl(),
            ),
            $sourceTag->build()->render(),
        );
    }

    #[Test]
    public function sourceFileWithAllTooLargeWidths(): void
    {
        $fileReference = $this->createFileReference();
        $cropVariant = 'image_test';
        $sourceTag = new SourceTag(
            source: new ImageSource(
                widths: [2000, 4000],
                artDirection: new ImageSource\ArtDirection(
                    cropVariant: $cropVariant,
                    media: '(min-width: 2000px)'
                ),
            ),
            fileReference: $fileReference,
        );
        self::assertSame(
            sprintf(
                '<source srcset="%s 1142w" media="(min-width: 2000px)" width="1142" height="889" />',
                $this->processExpectedFile($fileReference, 2000, $cropVariant)->getPublicUrl(),
            ),
            $sourceTag->build()->render(),
        );
    }

    #[Test]
    public function sourceFileWithOneOkAndMultipleTooLargeWidths(): void
    {
        $fileReference = $this->createFileReference('image1_310.jpg');
        $sourceTag = new SourceTag(
            source: new ImageSource(
                widths: [300, 600, 900, 1200],
            ),
            fileReference: $fileReference,
        );
        self::assertSame(
            sprintf(
                '<source srcset="%s 300w, %s 310w" width="300" height="235" />',
                $this->processExpectedFile($fileReference, 300)->getPublicUrl(),
                $this->processExpectedFile($fileReference, 600)->getPublicUrl(),
            ),
            $sourceTag->build()->render(),
        );
    }

    private function createFileReference(string $imageName = 'image1.jpg'): FileReference
    {
        $storage = GeneralUtility::makeInstance(StorageRepository::class)->findByUid(1);
        self::assertInstanceOf(ResourceStorage::class, $storage);
        $file = $storage->addFile(localFilePath: __DIR__ . '/../../Fixtures/Files/' . $imageName, targetFolder: $storage->getRootLevelFolder(), removeOriginal: false);
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
