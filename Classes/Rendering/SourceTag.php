<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageFormat;
use Helhum\TopImage\Definition\ImageSource;
use Helhum\TopImage\Rendering\RenderedImage\Identifier;
use TYPO3\CMS\Core\Resource\FileReference;

class SourceTag
{
    public function __construct(
        private readonly ImageSource $source,
        private readonly FileReference $fileReference,
        private readonly ?ImageFormat $format = null,
    ) {
    }

    public function build(): Tag
    {
        $srcsetDefinitions = [];
        $processing = new ProcessingInstructions(
            forFile: $this->fileReference,
            cropVariant: $this->source->artDirection?->cropVariant,
            format: $this->format,
        );
        $renderedImages = new RenderedImages();
        $widths = $this->source->widths;
        asort($widths, SORT_NUMERIC);
        foreach (array_values($widths) as $index => $width) {
            $renderedImage = $processing->forWidth($width)->execute();
            $minWidth = min((int)$renderedImage->getProperty('width'), $width);
            if ($index > 0 && $minWidth !== $width) {
                break;
            }
            $renderedImages = $renderedImages->add((new Identifier(source: $this->source, width: $width, format: $this->format)), $renderedImage);
            $srcsetDefinitions[] = sprintf('%s %dw', $renderedImage->getPublicUrl(), $minWidth);
            if ($minWidth !== $width) {
                break;
            }
        }
        $sourceTag = new Tag(
            'source',
            $renderedImages,
        );
        $sourceTag->addAttribute('srcset', implode(', ', $srcsetDefinitions));
        if ($this->source->sizes !== null) {
            $sourceTag->addAttribute('sizes', implode(', ', $this->source->sizes));
        }
        if ($this->source->artDirection?->media !== null) {
            $sourceTag->addAttribute('media', $this->source->artDirection->media);
        }
        if ($this->format?->needsTypeAttribute() === true) {
            $sourceTag->addAttribute('type', $this->format->mimeType());
        }
        $sourceTag->addAttribute('width', (string)$renderedImages->first()->getProperty('width'));
        $sourceTag->addAttribute('height', (string)$renderedImages->first()->getProperty('height'));

        return $sourceTag;
    }
}
