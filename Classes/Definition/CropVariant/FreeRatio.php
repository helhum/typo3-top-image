<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition\CropVariant;

class FreeRatio extends Ratio
{
    public function __construct(
    ) {
        parent::__construct(
            'NaN',
            'LLL:EXT:core/Resources/Private/Language/locallang_wizards.xlf:imwizard.ratio.free',
            0.0
        );
    }
}
