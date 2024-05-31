<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageSource;
use TYPO3\CMS\Core\Resource\FileReference;

class SourceTag
{
    public function __construct(
        private readonly ImageSource $source,
        private readonly FileReference $fileReference,
    ) {
    }

    public function build(): Tag
    {
        $sourceTag = new Tag('source');
        $srcsetDefinitions = [];
        $processing = new ProcessingInstructions(
            forFile: $this->fileReference,
            cropVariant: $this->source->artDirection?->cropVariant,
        );
        $renderedImages = [];
        foreach ($this->source->widths as $width) {
            $renderedImage = $processing->forWidth($width)->execute();
            $renderedImages[$width] = $renderedImage;
            $srcsetDefinitions[] = sprintf('%s %dw', $renderedImage->getPublicUrl(), $width);
        }
        $sourceTag->addAttribute('srcset', implode(', ', $srcsetDefinitions));
        if ($this->source->sizes !== null) {
            $sourceTag->addAttribute('sizes', implode(', ', $this->source->sizes));
        }
        if ($this->source->artDirection?->media !== null) {
            $sourceTag->addAttribute('media', $this->source->artDirection->media);
        }
        $sourceTag->addAttribute('width', (string)$renderedImages[array_key_first($renderedImages)]->getProperty('width'));
        $sourceTag->addAttribute('height', (string)$renderedImages[array_key_first($renderedImages)]->getProperty('height'));

        return $sourceTag;
    }
}
