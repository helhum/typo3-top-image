<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Unit\Definition;

use Helhum\TopImage\Definition\ContentField;
use Helhum\TopImage\Definition\ImageFormat;
use Helhum\TopImage\Definition\ImageSource;
use Helhum\TopImage\Definition\ImageVariant;
use Helhum\TopImage\Definition\InvalidDefinitionException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ImageVariantTest extends TestCase
{
    #[Test]
    public function throwsExceptionWhenSourceReferencesUndefinedCropVariant(): void
    {
        $this->expectException(InvalidDefinitionException::class);
        $imageVariant = new ImageVariant(
            id: 'test',
            appliesTo: [
                new ContentField(
                    table: 'test',
                    field: 'test',
                )
            ],
            sources: [
                new ImageSource(
                    widths: [500],
                    artDirection: new ImageSource\ArtDirection(
                        'test-crop',
                    ),
                ),
            ],
        );
    }

    #[Test]
    public function targetFormatsAreNullWhenNotGiven(): void
    {
        $imageVariant = new ImageVariant(id: 'test', appliesTo: []);
        self::assertNull($imageVariant->targetFormats);
    }

    #[Test]
    public function targetFormatsAreOrderedConversionFormatsFirstAndFallbackLast(): void
    {
        $imageVariant = new ImageVariant(id: 'test', appliesTo: [], targetFormats: [ImageFormat::JPG, ImageFormat::WEBP, ImageFormat::AVIF]);
        self::assertSame([ImageFormat::AVIF, ImageFormat::WEBP, ImageFormat::JPG], $imageVariant->targetFormats);
    }

    #[Test]
    public function originalFormatIsAddedAsFallbackWhenOnlyConversionFormatsAreGiven(): void
    {
        $imageVariant = new ImageVariant(id: 'test', appliesTo: [], targetFormats: [ImageFormat::WEBP]);
        self::assertSame([ImageFormat::WEBP, ImageFormat::ORIG], $imageVariant->targetFormats);
    }

    #[Test]
    public function duplicateTargetFormatsAreRemoved(): void
    {
        $imageVariant = new ImageVariant(id: 'test', appliesTo: [], targetFormats: [ImageFormat::WEBP, ImageFormat::ORIG, ImageFormat::WEBP]);
        self::assertSame([ImageFormat::WEBP, ImageFormat::ORIG], $imageVariant->targetFormats);
    }

    #[Test]
    public function throwsExceptionWhenMultipleFallbackFormatsAreGiven(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(1761048001);
        new ImageVariant(id: 'test', appliesTo: [], targetFormats: [ImageFormat::JPG, ImageFormat::PNG]);
    }

    #[Test]
    public function throwsExceptionWhenTargetFormatsAreEmpty(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionCode(1719594400);
        new ImageVariant(id: 'test', appliesTo: [], targetFormats: []);
    }

    #[Test]
    public function applyToKeepsTargetFormats(): void
    {
        $imageVariant = new ImageVariant(id: 'test', appliesTo: [], targetFormats: [ImageFormat::WEBP, ImageFormat::ORIG]);
        $appliedVariant = $imageVariant->applyTo([new ContentField(table: 'test', field: 'test')]);
        self::assertSame([ImageFormat::WEBP, ImageFormat::ORIG], $appliedVariant->targetFormats);
    }
}
