<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

enum ImageFormat: string
{
    case JPG = 'jpg';
    case PNG = 'png';
    case WEBP = 'webp';
    case AVIF = 'avif';
    /**
     * Keep the format of the original file
     */
    case ORIG = '';

    /**
     * Whether this format is a "next gen" format, that is rendered
     * as additional source for browsers supporting it, while a
     * fallback format is always rendered as well.
     */
    public function isConversionFormat(): bool
    {
        return match ($this) {
            self::WEBP, self::AVIF => true,
            default => false,
        };
    }

    public function needsTypeAttribute(): bool
    {
        return $this->isConversionFormat();
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::JPG => 'image/jpeg',
            self::ORIG => throw new \LogicException('Original file format has no fixed mime type', 1761048000),
            default => sprintf('image/%s', $this->value),
        };
    }
}
