<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

use Ds\Map;
use Ds\Set;

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
     * @param ImageSource[]|null $sources
     * @param CropVariant[]|null $cropVariants
     */
    public function __construct(
        public readonly string $id,
        public readonly ContentField $appliesTo,
        public readonly ?array $sources = null,
        public readonly ?array $cropVariants = null,
    ) {
        $this->cropVariantsMap = $this->createCropVariantsMap($this->cropVariants);
        $sourcesSet = $this->createSourcesSet($this->sources);
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
