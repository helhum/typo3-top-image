<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageSource\FallbackSource;
use Helhum\TopImage\Definition\ImageVariant;
use TYPO3\CMS\Core\Resource\FileReference;

class PictureTag
{
    /**
     * @param array<non-empty-string, string> $additionalTagAttributes
     */
    public function __construct(
        private readonly ImageVariant $imageVariant,
        private readonly FileReference $fileReference,
        private readonly array $additionalTagAttributes = [],
    ) {
    }

    public function build(): Tag
    {
        if ($this->imageVariant->sources === null) {
            throw new RenderingException(sprintf('Can not render a picture tag for image variant %s because it has no sources defined', $this->imageVariant->id), 1716591424);
        }
        $pictureTag = new Tag('picture');
        $tagContent = '';
        foreach ($this->imageVariant->sources as $source) {
            $tagContent .= (new SourceTag(source: $source, fileReference: $this->fileReference))->build()->render();
        }
        $fallbackSource = $this->imageVariant->fallbackSource;
        if ($fallbackSource === null) {
            $source = $this->imageVariant->sources[array_key_last($this->imageVariant->sources)];
            $fallbackSource = new FallbackSource(
                $source->widths[0],
                $source->artDirection?->cropVariant,
            );
        }
        $imageTag = (new ImgTag(source: $fallbackSource, fileReference: $this->fileReference))->build();
        foreach ($this->additionalTagAttributes as $name => $value) {
            $imageTag->addAttribute($name, $value);
        }
        $tagContent .= $imageTag->render();
        $pictureTag->setContent($tagContent);

        return $pictureTag;
    }
}
