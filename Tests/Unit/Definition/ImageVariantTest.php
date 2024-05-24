<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Unit\Definition;

use Helhum\TopImage\Definition\ContentField;
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
            appliesTo: new ContentField(
                table: 'test',
                field: 'test',
            ),
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
}
