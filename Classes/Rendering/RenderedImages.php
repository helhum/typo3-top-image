<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use Ds\Map;
use Helhum\TopImage\Definition\ImageFormat;
use Helhum\TopImage\Definition\ImageSource;
use Helhum\TopImage\Rendering\RenderedImage\Identifier;
use Traversable;
use TYPO3\CMS\Core\Resource\ProcessedFile;

/**
 * @implements \IteratorAggregate<array{ImageSource | ImageSource\FallbackSource, int, ?ImageFormat}, ProcessedFile>
 */
class RenderedImages implements \IteratorAggregate
{
    /**
     * @var Map<array{ImageSource|ImageSource\FallbackSource, int, ?ImageFormat}, ProcessedFile>
     */
    private Map $map;

    /**
     * @param Map<array{ImageSource|ImageSource\FallbackSource, int, ?ImageFormat}, ProcessedFile>|null $renderedImages
     */
    public function __construct(
        ?Map $renderedImages = null,
    ) {
        $this->map = $renderedImages ?? new Map();
    }

    public function get(Identifier $identifiedBy): ProcessedFile
    {
        if (!$this->map->hasKey($identifiedBy->toArray())) {
            throw new \LogicException(sprintf('No rendered image found for this width %d', $identifiedBy->width), 1717164649);
        }
        return $this->map->get($identifiedBy->toArray());
    }

    public function first(): ProcessedFile
    {
        return $this->map->first()->value;
    }

    /**
     * @internal
     */
    public function add(Identifier $identifiedBy, ProcessedFile $file): self
    {
        $newMap = $this->map->copy();
        $newMap->put($identifiedBy->toArray(), $file);
        return new self($newMap);
    }

    /**
     * @internal
     */
    public function merge(self $renderedImages): self
    {
        return new self($this->map->merge($renderedImages->map));
    }

    public function getIterator(): Traversable
    {
        return $this->map->getIterator();
    }
}
