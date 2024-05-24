<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageSource;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class SourceTag
{
    public function __construct(
        private readonly ImageSource $source,
        private readonly FileReference $fileReference,
    ) {
    }

    public function render(): string
    {
        $tagBuilder = new TagBuilder('source');
        $srcsetDefinitions = [];
        foreach ($this->source->widths as $width) {
            $srcsetDefinitions[] = sprintf('%s %dw', $this->process($width)->getPublicUrl(), $width);
        }
        $tagBuilder->addAttribute('srcset', implode(', ', $srcsetDefinitions));
        if ($this->source->sizes !== null) {
            $tagBuilder->addAttribute('sizes', implode(', ', $this->source->sizes));
        }
        if ($this->source->artDirection?->media !== null) {
            $tagBuilder->addAttribute('media', $this->source->artDirection->media);
        }

        return $tagBuilder->render();
    }

    private function process(int $width): ProcessedFile
    {
        $cropVariantCollection = CropVariantCollection::create((string)($this->fileReference->getProperty('crop') ?? ''));
        $cropVariant = $this->source->artDirection->cropVariant ?? 'default';
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        $processingInstructions = [
            'maxWidth' => $width,
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($this->fileReference),
        ];

        return $this->fileReference->getOriginalFile()->process(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK, $processingInstructions);
    }
}
