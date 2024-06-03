<?php
declare(strict_types=1);

namespace Helhum\TopImage\Tests\Unit\Rendering;

use Ds\Map;
use Helhum\TopImage\Definition\ImageSource;
use Helhum\TopImage\Rendering\RenderedImage\Identifier;
use Helhum\TopImage\Rendering\RenderedImages;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Resource\ProcessedFile;

class RenderedImagesTest extends TestCase
{
    #[Test]
    public function storedObjectCanBeRetrievedByKey(): void
    {
        $processedFile = $this->getProcessedFileMock();
        $renderedImages = new RenderedImages();
        $id = new Identifier(source: new ImageSource([300]), width: 300);
        $subject = $renderedImages->add($id, $processedFile);
        self::assertSame($processedFile, $subject->get($id));
        self::assertSame($processedFile, $subject->first());
    }

    #[Test]
    public function addingValuesDoesNotModifyOriginal(): void
    {
        $processedFile = $this->getProcessedFileMock();
        $renderedImages = new RenderedImages();
        $id = new Identifier(source: new ImageSource([300]), width: 300);
        $renderedImages->add($id, $processedFile);
        $this->expectException(\UnderflowException::class);
        $renderedImages->first();
    }

    #[Test]
    public function mergingMergesObjectIntoEmptyStore(): void
    {
        $processedFile = $this->getProcessedFileMock();
        $renderedImages = new RenderedImages();
        $key = new Identifier(source: new ImageSource([300]), width: 300);
        $toBeMerged = new Map();
        $toBeMerged->put($key->toArray(), $processedFile);
        $renderedImages = $renderedImages->merge(new RenderedImages($toBeMerged));
        self::assertSame($processedFile, $renderedImages->get($key));
    }

    private function getProcessedFileMock(): ProcessedFile
    {
        return $this->getMockBuilder(ProcessedFile::class)->disableOriginalConstructor()->getMock();
    }
}
