<?php
declare(strict_types=1);

namespace Helhum\TopImage\ViewHelpers;

use Helhum\TopImage\Definition\ImageVariantCollection;
use Helhum\TopImage\Rendering\PictureTag;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class ResponsiveImageViewHelper extends AbstractTagBasedViewHelper
{
    public function __construct(private ImageVariantCollection $imageVariantCollection)
    {
        parent::__construct();
    }

    public function initializeArguments(): void
    {
        $this->registerUniversalTagAttributes();
        $this->registerArgument(
            'image',
            FileReference::class,
            'The image to render',
            true
        );
        $this->registerArgument(
            'variant',
            'string',
            'image variant to render',
            true
        );
        $this->registerArgument(
            'additionalAttributes',
            'array',
            'additional tag attributes',
            false,
            []
        );
    }

    public function render(): string
    {
        $pictureTag = new PictureTag(
            imageVariant: $this->imageVariantCollection->imageVariants->get($this->arguments['variant']),
            fileReference: $this->arguments['image'],
            additionalTagAttributes: $this->tag->getAttributes(),
        );
        return $pictureTag->build()->render();
    }
}
