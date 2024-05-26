<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageSource\FallbackSource;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class ImgTag
{
    public function __construct(
        private readonly FallbackSource $source,
        private readonly FileReference $fileReference,
    ) {
    }

    public function build(): TagBuilder
    {
        $imgTag = new TagBuilder('img');
        $processing = new ProcessingInstructions(
            forFile: $this->fileReference,
            width: $this->source->width,
            cropVariant: $this->source->cropVariant,
        );
        $image = $processing->execute();
        $imgTag->addAttribute('src', $image->getPublicUrl());
        $imgTag->addAttribute('width', $image->getProperty('width'));
        $imgTag->addAttribute('height', $image->getProperty('height'));
        if ($this->fileReference->hasProperty('alternative')) {
            $imgTag->addAttribute('alt', $this->fileReference->getProperty('alternative'));
        }
        if ($this->fileReference->hasProperty('title')) {
            $imgTag->addAttribute('title', $this->fileReference->getProperty('title'));
        }

        return $imgTag;
    }
}
