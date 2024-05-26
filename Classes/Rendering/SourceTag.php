<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageSource;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class SourceTag
{
    public function __construct(
        private readonly ImageSource $source,
        private readonly FileReference $fileReference,
    ) {
    }

    public function build(): TagBuilder
    {
        $sourceTag = new TagBuilder('source');
        $srcsetDefinitions = [];
        $processing = new ProcessingInstructions(
            forFile: $this->fileReference,
            cropVariant: $this->source->artDirection?->cropVariant,
        );
        foreach ($this->source->widths as $width) {
            $srcsetDefinitions[] = sprintf('%s %dw', $processing->forWidth($width)->execute()->getPublicUrl(), $width);
        }
        $sourceTag->addAttribute('srcset', implode(', ', $srcsetDefinitions));
        if ($this->source->sizes !== null) {
            $sourceTag->addAttribute('sizes', implode(', ', $this->source->sizes));
        }
        if ($this->source->artDirection?->media !== null) {
            $sourceTag->addAttribute('media', $this->source->artDirection->media);
        }

        return $sourceTag;
    }
}
