<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

class ContentField
{
    public function __construct(
        public readonly string $table,
        public readonly string $field,
        public readonly ?string $type = null,
    ) {
    }
}
