<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ImageSource
{
    /**
     * @param int[] $widths A list of widths, the browser can pick from to match best resolution
     * @param string[] $sizes Array of sizes (https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/sizes)
     */
    public function __construct(
        public readonly array $widths,
        public readonly ?array $sizes = null,
        public readonly ?ImageSource\ArtDirection $artDirection = null,
    ) {
    }
}
