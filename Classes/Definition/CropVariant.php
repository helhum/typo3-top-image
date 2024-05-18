<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

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

    public function toTca(): array
    {
        $aspectRatios = [];
        foreach ($this->allowedAspectRatios as $aspectRatio) {
            $aspectRatios[] = $aspectRatio->toTca();
        }
        $allowedAspectRatios = array_merge([], ...$aspectRatios);
        return [
            'title' => $this->title,
            'allowedAspectRatios' => $allowedAspectRatios,
        ];
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
                throw new InvalidDefinitionException(sprintf('Ratio with with duplicate ID (%s) is configured. Make sure all configured ratios have different ids.', $ratio->id), 1716032871);
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
