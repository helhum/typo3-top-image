<?php

declare(strict_types=1);

namespace Helhum\TopImage\TCA;

use Helhum\TopImage\Definition\CropVariant;
use Helhum\TopImage\Definition\CropVariant\Area;
use Helhum\TopImage\Definition\CropVariant\Ratio;
use Helhum\TopImage\Definition\ImageVariant;
use Helhum\TopImage\Definition\TCA;

class CropVariantGenerator
{
    /**
     * @param ImageVariant[] $imageVariants
     */
    public function __construct(private readonly array $imageVariants)
    {
    }

    public function createTca(TCA $tca): TCA
    {
        foreach ($this->imageVariants as $imageVariant) {
            $typesPath = sprintf('%s.types', $imageVariant->appliesTo->table);
            $types = $tca->get(
                $typesPath,
                null,
            );
            if (!is_array($types)) {
                continue;
            }
            foreach ($types as $type => $_) {
                if ($imageVariant->cropVariants === null) {
                    continue;
                }
                if ($imageVariant->appliesTo->type !== null && $imageVariant->appliesTo->type !== (string)$type) {
                    continue;
                }
                foreach ($imageVariant->cropVariants as $cropVariant) {
                    $tca = $tca->set(
                        sprintf('%s.%s.columnsOverrides.%s.config.overrideChildTca.columns.crop.config.cropVariants.%s', $typesPath, $type, $imageVariant->appliesTo->field, $cropVariant->id),
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
