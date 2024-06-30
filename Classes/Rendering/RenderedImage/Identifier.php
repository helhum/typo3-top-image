<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering\RenderedImage;

use Helhum\TopImage\Definition\ImageFormat;
use Helhum\TopImage\Definition\ImageSource;

class Identifier
{
    public function __construct(
        public readonly ImageSource | ImageSource\FallbackSource $source,
        public readonly int $width,
        public readonly ?ImageFormat $format = null,
    ) {
    }

    public static function fromFallbackSource(ImageSource\FallbackSource $source): self
    {
        return new self(source: $source, width: $source->width);
    }

    /**
     * @internal
     * @return array{ImageSource|ImageSource\FallbackSource, int, ?ImageFormat}
     */
    public function toArray(): array
    {
        return [
            $this->source,
            $this->width,
            $this->format,
        ];
    }
}
