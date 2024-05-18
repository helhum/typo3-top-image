<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Unit\Definition;

use Helhum\TopImage\Definition\CropVariant;
use Helhum\TopImage\Definition\FreeRatio;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CropVariantTest extends TestCase
{
    #[Test]
    public function minimalVariantCanBeCreated(): void
    {
        $variant = new CropVariant(
            'id',
            'title'
        );
        self::assertSame('id', $variant->id);
        self::assertSame('title', $variant->title);
        self::assertCount(1, $variant->allowedAspectRatios);
        self::assertInstanceOf(FreeRatio::class, $variant->allowedAspectRatios[0]);
    }
}
