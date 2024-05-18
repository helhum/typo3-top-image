<?php
declare(strict_types=1);

namespace Helhum\TopImage\TCA;

use Helhum\TopImage\Definition\Area;
use Helhum\TopImage\Definition\CropVariant;
use Helhum\TopImage\Definition\ImageManipulation;
use Helhum\TopImage\Definition\Ratio;
use Helhum\TopImage\Definition\TCA;

class CropVariantGenerator
{
    /**
     * @param ImageManipulation[] $imageManipulationDefinitions
     */
    public function __construct(private readonly array $imageManipulationDefinitions)
    {
    }

    public function createImageManipulationOverrides(TCA $tca): TCA
    {
        foreach ($this->imageManipulationDefinitions as $imageManipulationDefinition) {
            $typesPath = sprintf('%s.types', $imageManipulationDefinition->table);
            $types = $tca->get(
                $typesPath,
                null,
            );
            if (!is_array($types)) {
                continue;
            }
            foreach ($types as $type => $_) {
                if ($imageManipulationDefinition->type !== null && $imageManipulationDefinition->type !== (string)$type) {
                    continue;
                }
                foreach ($imageManipulationDefinition->cropVariants as $cropVariant) {
                    $tca = $tca->set(
                        sprintf('%s.%s.columnsOverrides.%s.config.overrideChildTca.columns.crop.config.cropVariants.%s', $typesPath, $type, $imageManipulationDefinition->field, $cropVariant->id),
                        $this->cropVariantToTca($cropVariant)
                    );
                }
            }
        }

        return $tca;
    }

    /**
     * @param CropVariant $cropVariant
     * @return array{title: string, allowedAspectRatios: array<string, array{title: string, value: float}>}
     */
    private function cropVariantToTca(CropVariant $cropVariant): array
    {
        $aspectRatios = [];
        foreach ($cropVariant->allowedAspectRatios as $aspectRatio) {
            $aspectRatios[] = $this->aspectRatioToTca($aspectRatio);
        }
        $allowedAspectRatios = array_merge([], ...$aspectRatios);
        $cropVariantTca = [
            'title' => $cropVariant->title,
            'allowedAspectRatios' => $allowedAspectRatios,
        ];
        if ($cropVariant->focusArea !== null) {
            $cropVariantTca['focusArea'] = $this->areaToTca($cropVariant->focusArea);
        }
        if ($cropVariant->coverAreas !== null) {
            $coverAreas = [];
            foreach ($cropVariant->coverAreas as $coverArea) {
                $coverAreas[] = $this->areaToTca($coverArea);
            }
            $cropVariantTca['focusArea'] = $coverAreas;
        }

        return $cropVariantTca;
    }

    /**
     * @return non-empty-array<string, array{title: string, value: float}>
     */
    private function aspectRatioToTca(Ratio $aspectRation): array
    {
        return [
            $aspectRation->id => [
                'title' => $aspectRation->title,
                'value' => $aspectRation->value,
            ],
        ];
    }

    /**
     * @return array{x: float, y: float, width: float, height: float}
     */
    private function areaToTca(Area $area): array
    {
        return [
            'x' => $area->offsetLeft,
            'y' => $area->offsetTop,
            'width' => $area->width,
            'height' => $area->height,
        ];
    }
}
