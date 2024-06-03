<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

use Helhum\TopImage\Definition\CropVariant\Area;
use Helhum\TopImage\Definition\CropVariant\FreeRatio;
use Helhum\TopImage\Definition\CropVariant\Ratio;

class CropVariant
{
    /**
     * @var Ratio[]
     */
    public readonly array $allowedAspectRatios;

    /**
     * @var Area[]|null
     */
    public readonly ?array $coverAreas;

    /**
     * @param Ratio[] $allowedAspectRatios
     * @param Area[]|null $coverAreas
     * @throws InvalidDefinitionException
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        array $allowedAspectRatios = [new FreeRatio()],
        public readonly ?Area $focusArea = null,
        ?array $coverAreas = null,
    ) {
        $this->allowedAspectRatios = $this->assertAspectRatios(...$allowedAspectRatios);
        $this->coverAreas = $coverAreas !== null ? $this->assertCoverAreas(...$coverAreas) : null;
    }

    /**
     * @return Ratio[]
     * @throws InvalidDefinitionException
     */
    private function assertAspectRatios(Ratio ...$ratios): array
    {
        $allowedAspectRatios = [];
        foreach ($ratios as $ratio) {
            if (isset($allowedAspectRatios[$ratio->id])) {
                throw new InvalidDefinitionException(sprintf('Ratio with non-unique ID (%s) is configured. Make sure all configured ratios have unique ids.', $ratio->id), 1716032871);
            }
            $allowedAspectRatios[] = $ratio;
        }
        return $allowedAspectRatios;
    }

    /**
     * @return Area[]
     */
    private function assertCoverAreas(Area ...$areas): array
    {
        $coverAreas = [];
        foreach ($areas as $area) {
            $coverAreas[] = $area;
        }
        return $coverAreas;
    }
}
