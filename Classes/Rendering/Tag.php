<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering;

use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

final class Tag
{
    private readonly TagBuilder $tagBuilder;

    public function __construct(
        string $tagName,
        public readonly RenderedImages $renderedImages,
    ) {
        $this->tagBuilder = new TagBuilder($tagName);
        $this->tagBuilder->ignoreEmptyAttributes(true);
    }

    public function render(): string
    {
        return $this->tagBuilder->render();
    }

    public function addAttribute(string $name, string $value): self
    {
        $this->tagBuilder->addAttribute($name, $value);
        return $this;
    }

    public function setContent(string $content): void
    {
        $this->tagBuilder->setContent($content);
    }
}
