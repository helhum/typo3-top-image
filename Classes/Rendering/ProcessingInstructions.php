<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Helhum\TopImage\Definition\ImageFormat;
use TYPO3\CMS\Core\Imaging\ImageManipulation\Area;
use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\ProcessedFile;

class ProcessingInstructions
{
    public function __construct(
        private readonly FileReference $forFile,
        private readonly int $width = 320,
        private readonly ?string $cropVariant = null,
        private readonly ?ImageFormat $format = null,
    ) {
    }

    public function forWidth(int $width): self
    {
        return new self(
            forFile: $this->forFile,
            width: $width,
            cropVariant: $this->cropVariant,
            format: $this->format,
        );
    }

    public function execute(): ProcessedFile
    {
        return $this->forFile->getOriginalFile()->process(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK, $this->buildInstructionsWithCrop());
    }

    /**
     * @return array{maxWidth: int, crop: Area|null}
     */
    private function buildInstructionsWithCrop(): array
    {
        $cropVariantCollection = CropVariantCollection::create((string)($this->forFile->getProperty('crop') ?? ''));
        $cropVariant = $this->cropVariant ?? 'default';
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        $instructions = [
            'maxWidth' => $this->width,
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($this->forFile),
        ];

        if ($this->format !== null) {
            $instructions['fileExtension'] = $this->format->value;
        }

        return $instructions;
    }
}
