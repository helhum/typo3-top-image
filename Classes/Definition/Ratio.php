<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class Ratio
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly float $value,
    ) {
    }

    /**
     * @return non-empty-array<string, array{title: string, value: float}>
     */
    public function toTca(): array
    {
        return [
            $this->id => [
                'title' => $this->title,
                'value' => $this->value,
            ],
        ];
    }
}
