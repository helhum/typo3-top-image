<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ImageSource
{
    /**
     * @param int[] $widths A list of widths, the browser can pick from to match best resolution
     * @param string[] $sizes Array of sizes (https://developer.mozilla.org/en-US/docs/Web/API/HTMLImageElement/sizes)
     * @param string|null $media Media query for this source to be selected (https://developer.mozilla.org/en-US/docs/Web/HTML/Element/source#media)
     */
    public function __construct(
        public readonly array $widths,
        public readonly ?array $sizes = null,
        public readonly ?string $media = null,
    ) {
    }
}
