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
    //
    //    /**
    //     * @var Set<ImageSource>
    //     */
    //    private readonly Set $sourcesSet;

    /**
     * @param ContentField[] $appliesTo
     * @param ImageSource[]|null $sources
     * @param CropVariant[]|null $cropVariants
     */
    public function __construct(
        public readonly string $id,
        public readonly array $appliesTo,
        public readonly ?array $sources = null,
        public readonly ?FallbackSource $fallbackSource = null,
        public readonly ?array $cropVariants = null,
    ) {
        $this->cropVariantsMap = $this->createCropVariantsMap($this->cropVariants);
        if ($this->fallbackSource?->cropVariant !== null && !$this->cropVariantsMap->hasKey($this->fallbackSource->cropVariant)) {
            throw new InvalidDefinitionException(sprintf('Invalid crop variant "%s" defined in fallback source', $this->fallbackSource->cropVariant), 1716650213);
        }
        $sourcesSet = $this->createSourcesSet($this->sources);
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
