<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

use Ds\Map;
use Ds\Set;
use Helhum\TopImage\Definition\ImageSource\FallbackSource;

class ImageVariant
{
    /**
     * @var Map<string, CropVariant>
     */
    private readonly Map $cropVariantsMap;

    /**
     * @var non-empty-list<ImageFormat>|null
     */
    public readonly ?array $targetFormats;

    //
    //    /**
    //     * @var Set<ImageSource>
    //     */
    //    private readonly Set $sourcesSet;

    /**
     * @param ContentField[] $appliesTo
     * @param ImageSource[]|null $sources
     * @param CropVariant[]|null $cropVariants
     * @param ImageFormat[]|null $targetFormats
     */
    public function __construct(
        public readonly string $id,
        public readonly array $appliesTo,
        public readonly ?array $sources = null,
        public readonly ?FallbackSource $fallbackSource = null,
        public readonly ?array $cropVariants = null,
        ?array $targetFormats = null,
    ) {
        $this->cropVariantsMap = $this->createCropVariantsMap($this->cropVariants);
        if ($this->fallbackSource?->cropVariant !== null && !$this->cropVariantsMap->hasKey($this->fallbackSource->cropVariant)) {
            throw new InvalidDefinitionException(sprintf('Invalid crop variant "%s" defined in fallback source', $this->fallbackSource->cropVariant), 1716650213);
        }
        $this->targetFormats = $targetFormats === null ? null : $this->normalizeTargetFormats($targetFormats);
        $sourcesSet = $this->createSourcesSet($this->sources);
    }

    /**
     * Brings target formats into rendering order:
     * conversion formats (AVIF before WEBP) first, exactly one fallback format last.
     * When only conversion formats are given, the original file format is used as fallback.
     *
     * @param ImageFormat[] $targetFormats
     * @return non-empty-list<ImageFormat>
     */
    private function normalizeTargetFormats(array $targetFormats): array
    {
        if ($targetFormats === []) {
            throw new \UnexpectedValueException('Target formats must not be empty.', 1719594400);
        }
        $conversionFormats = [];
        $fallbackFormats = [];
        foreach ($targetFormats as $targetFormat) {
            if ($targetFormat->isConversionFormat()) {
                $conversionFormats[$targetFormat->value] = $targetFormat;
            } else {
                $fallbackFormats[$targetFormat->value] = $targetFormat;
            }
        }
        if (count($fallbackFormats) > 1) {
            throw new \UnexpectedValueException(sprintf('Only one fallback target format is allowed, %d given.', count($fallbackFormats)), 1761048001);
        }
        $orderedFormats = [];
        foreach ([ImageFormat::AVIF, ImageFormat::WEBP] as $conversionFormat) {
            if (isset($conversionFormats[$conversionFormat->value])) {
                $orderedFormats[] = $conversionFormat;
            }
        }
        $orderedFormats[] = array_values($fallbackFormats)[0] ?? ImageFormat::ORIG;

        return $orderedFormats;
    }

    /**
     * @param ContentField[] $contentFields
     */
    public function applyTo(array $contentFields): self
    {
        return new self(
            $this->id,
            array_merge($this->appliesTo, $contentFields),
            $this->sources,
            $this->fallbackSource,
            $this->cropVariants,
            $this->targetFormats,
        );
    }

    /**
     * @param CropVariant[]|null $cropVariants
     * @return Map<string, CropVariant>
     */
    private function createCropVariantsMap(?array $cropVariants): Map
    {
        $cropVariantsMap = new Map();
        if ($cropVariants === null) {
            return $cropVariantsMap;
        }
        foreach ($cropVariants as $cropVariant) {
            $cropVariantsMap->put($cropVariant->id, $cropVariant);
        }

        return $cropVariantsMap;
    }

    /**
     * @param ImageSource[]|null $sources
     * @return Set<ImageSource>
     * @throws InvalidDefinitionException
     */
    private function createSourcesSet(?array $sources): Set
    {
        foreach ($sources ?? [] as $source) {
            if ($source->artDirection?->cropVariant !== null
                && !$this->cropVariantsMap->hasKey($source->artDirection->cropVariant)
            ) {
                throw new InvalidDefinitionException(sprintf('Invalid crop variant "%s" defined in source', $source->artDirection->cropVariant), 1716564872);
            }
        }
        return new Set($sources ?? []);
    }
}
