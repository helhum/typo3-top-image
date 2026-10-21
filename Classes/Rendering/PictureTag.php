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
        // Target formats are normalized by ImageVariant: conversion formats first, fallback format last
        $targetFormats = $this->imageVariant->targetFormats ?? [null];
        $fallbackFormat = $targetFormats[array_key_last($targetFormats)];
        $isSvg = $this->fileReference->getExtension() === 'svg';
        foreach ($this->imageVariant->sources as $source) {
            $conversionSources = [];
            foreach ($targetFormats as $targetFormat) {
                if ($isSvg && $targetFormat?->isConversionFormat() === true) {
                    continue;
                }
                $sourceTag = (new SourceTag(source: $source, fileReference: $this->fileReference, format: $targetFormat, imageVariant: $this->imageVariant->id))->build();
                if ($targetFormat !== $fallbackFormat) {
                    $conversionSources[] = $sourceTag;
                    continue;
                }
                foreach ($conversionSources as $conversionSource) {
                    if ($this->hasLargerFiles($conversionSource, $sourceTag, $fallbackFormat)) {
                        continue;
                    }
                    $renderedImages = $renderedImages->merge($conversionSource->renderedImages);
                    $tagContent .= $conversionSource->render();
                }
                $renderedImages = $renderedImages->merge($sourceTag->renderedImages);
                $tagContent .= $sourceTag->render();
            }
        }
        $fallbackSource = $this->imageVariant->fallbackSource;
        if ($fallbackSource === null) {
            $sources = $this->imageVariant->sources;
            $source = array_pop($sources);
            if ($source === null) {
                throw new RenderingException(sprintf('Can not render a picture tag for image variant %s because it has no sources defined', $this->imageVariant->id), 1761048002);
            }
            $fallbackSource = new FallbackSource(
                $source->widths[0],
                $source->artDirection?->cropVariant,
            );
        }
        $imageTag = (new ImgTag(source: $fallbackSource, fileReference: $this->fileReference, format: $fallbackFormat, imageVariant: $this->imageVariant->id))->build();
        foreach ($this->additionalTagAttributes as $name => $value) {
            $imageTag->addAttribute($name, $value);
        }
        $tagContent .= $imageTag->render();
        $pictureTag = new Tag('picture', $renderedImages);
        $pictureTag->setContent($tagContent);

        return $pictureTag;
    }

    /**
     * Checks whether any of the converted images is larger than
     * its counterpart in the fallback format. If so, rendering the
     * conversion source would be a waste of bandwidth.
     */
    private function hasLargerFiles(Tag $conversionSource, Tag $fallbackSource, ?ImageFormat $fallbackFormat): bool
    {
        foreach ($conversionSource->renderedImages as $key => $convertedImage) {
            [$source, $width] = $key;
            $identifier = new Identifier($source, $width, $fallbackFormat);
            try {
                if ($convertedImage->getSize() > $fallbackSource->renderedImages->get($identifier)->getSize()) {
                    return true;
                }
            } catch (\Exception) {
                // Missing files must not break rendering
            }
        }
        return false;
    }
}
