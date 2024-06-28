<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

enum ImageFormat: string
{
    case JPG = 'jpg';
    case WEBP = 'webp';

    public function needsTypeAttribute(): bool
    {
        return match ($this) {
            self::WEBP => true,
            default => false,
        };
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::JPG => 'image/jpeg',
            default => sprintf('image/%s', $this->value),
        };
    }
}
