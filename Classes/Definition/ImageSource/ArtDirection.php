<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition\ImageSource;

class ArtDirection
{
    /**
     * @param string $cropVariant Identifier of a crop variant defined for this ImageVariant
     * @param string|null $media Media query for this source to be selected (https://developer.mozilla.org/en-US/docs/Web/HTML/Element/source#media)
     */
    public function __construct(
        public readonly string $cropVariant,
        public readonly ?string $media = null,
    ) {
    }
}
