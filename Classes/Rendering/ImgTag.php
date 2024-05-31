<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageSource\FallbackSource;
use TYPO3\CMS\Core\Resource\FileReference;

class ImgTag
{
    public function __construct(
        private readonly FallbackSource $source,
        private readonly FileReference $fileReference,
    ) {
    }

    public function build(): Tag
    {
        $imgTag = new Tag('img');
        $processing = new ProcessingInstructions(
            forFile: $this->fileReference,
            width: $this->source->width,
            cropVariant: $this->source->cropVariant,
        );
        $image = $processing->execute();
        $imgTag->addAttribute('src', (string)$image->getPublicUrl());
        $imgTag->addAttribute('width', (string)$image->getProperty('width'));
        $imgTag->addAttribute('height', (string)$image->getProperty('height'));
        if ($this->fileReference->hasProperty('alternative')) {
            $imgTag->addAttribute('alt', (string)$this->fileReference->getProperty('alternative'));
        }
        if ($this->fileReference->hasProperty('title')) {
            $imgTag->addAttribute('title', (string)$this->fileReference->getProperty('title'));
        }

        return $imgTag;
    }
}
