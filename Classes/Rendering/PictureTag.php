<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Ds\Map;
use Helhum\TopImage\Definition\ImageFormat;
use Helhum\TopImage\Definition\ImageSource\FallbackSource;
use Helhum\TopImage\Definition\ImageVariant;
use Helhum\TopImage\Rendering\RenderedImage\Identifier;
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
        $tagContent = '';
        $renderedImages = new RenderedImages(new Map());
        foreach ($this->imageVariant->sources as $source) {
            $targetFormats = $this->imageVariant->targetFormats ?? [null];
            $webpSource = null;
            foreach ($targetFormats as $targetFormat) {
                $sourceTag = (new SourceTag(source: $source, fileReference: $this->fileReference, format: $targetFormat, imageVariant: $this->imageVariant->id))->build();
                if ($webpSource === null && $targetFormat === ImageFormat::WEBP) {
                    $webpSource = $sourceTag;
                    continue;
                }
                if ($webpSource !== null && !$this->hasLargerFiles($webpSource, $sourceTag)) {
                    $renderedImages = $renderedImages->merge($webpSource->renderedImages);
                    $tagContent .= $webpSource->render();
                }
                $renderedImages = $renderedImages->merge($sourceTag->renderedImages);
                $tagContent .= $sourceTag->render();
            }
        }
        $fallbackSource = $this->imageVariant->fallbackSource;
        if ($fallbackSource === null) {
            $source = $this->imageVariant->sources[array_key_last($this->imageVariant->sources)];
            $fallbackSource = new FallbackSource(
                $source->widths[0],
                $source->artDirection?->cropVariant,
            );
        }
        $targetFormat = $this->imageVariant->targetFormats === null ? null : ImageFormat::JPG;
        $imageTag = (new ImgTag(source: $fallbackSource, fileReference: $this->fileReference, format: $targetFormat, imageVariant: $this->imageVariant->id))->build();
        foreach ($this->additionalTagAttributes as $name => $value) {
            $imageTag->addAttribute($name, $value);
        }
        $tagContent .= $imageTag->render();
        $pictureTag = new Tag('picture', $renderedImages);
        $pictureTag->setContent($tagContent);

        return $pictureTag;
    }

    private function hasLargerFiles(Tag $webpSource, Tag $jpegSource): bool
    {
        foreach ($webpSource->renderedImages as $key => $webpImage) {
            [$source, $width] = $key;
            $identifier = new Identifier($source, $width, ImageFormat::JPG);
            if ($webpImage->getSize() > $jpegSource->renderedImages->get($identifier)->getSize()) {
                return true;
            }
        }
        return false;
    }
}
