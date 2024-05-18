<?php
declare(strict_types=1);

namespace Helhum\TopImage\TCA;

use Helhum\TopImage\Definition\ImageManipulation;

class CropVariantGenerator
{
    /**
     * @param ImageManipulation[] $imageManipulationDefinitions
     */
    public function __construct(private readonly array $imageManipulationDefinitions)
    {

    }

    /**
     * @param array<mixed> $tca
     * @return array<mixed>
     */
    public function createImageManipulationOverrides(array $tca): array
    {
        foreach ($this->imageManipulationDefinitions as $imageManipulationDefinition) {
            if (!is_array($tca[$imageManipulationDefinition->table]['types'] ?? null)) {
                continue;
            }
            foreach ($tca[$imageManipulationDefinition->table]['types'] as $type => &$typeConfig) {
                if ($imageManipulationDefinition->type !== null && $imageManipulationDefinition->type !== $type) {
                    continue;
                }
                foreach ($imageManipulationDefinition->cropVariants as $cropVariant) {
                    $aspectRatios = [];
                    foreach ($cropVariant->allowedAspectRatios as $aspectRatio) {
                        $aspectRatios[$aspectRatio->id] = [
                            'title' => $aspectRatio->title,
                            'value' => $aspectRatio->value,
                        ];
                    }
                    $typeConfig['columnsOverrides'][$imageManipulationDefinition->field]['config']['overrideChildTca']['columns']['crop']['config']['cropVariants'][$cropVariant->id] = [
                        'title' => $cropVariant->title,
                        'allowedAspectRatios' => $aspectRatios,
                    ];
                }

            }
            unset($typeConfig);
        }

        return $tca;
    }
}
