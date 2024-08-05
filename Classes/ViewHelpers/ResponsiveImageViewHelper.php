<?php
declare(strict_types=1);

namespace Helhum\TopImage\ViewHelpers;

use Helhum\TopImage\Definition\ImageVariantCollection;
use Helhum\TopImage\Rendering\PictureTag;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Renders a picture tag for a given image variant identifier.
 *
 * Which sources are rendered are defined in configuration.
 * To be able to respect the different crop areas, the only
 * possible input to this view helper are FileReference objects.
 *
 * This ViewHelper is NOT designed to work in TYPO3 backend!
 *
 * Examples
 * ========
 *
 * Default
 * -------
 *
 * ::
 *
 *    <ti:responsiveImage image="{imageReference}" imageVariant="news-list" class="class-of-image" />
 *
 * Results in the following output within TYPO3 frontend (highly depends on configuration):
 *
 * ``<picture><source srcset="/fileadmin/_processed_/a/b/image_asdf.jpg 300w, /fileadmin/_processed_/a/b/image_ghij.jpg 600w" sizes="(min-width: 760px) 50vw, 100vw" media="(max-width: 2048px)" width="300" height="234" /><source srcset="%3$s 1100w" width="1100" height="857" /><img class="class-of-image" src="/fileadmin/_processed_/a/b/image_asdf.jpg" width="300" height="234" alt="alt of image" title="title of image" /></picture>``
 */
class ResponsiveImageViewHelper extends AbstractTagBasedViewHelper
{
    public function __construct(private readonly ImageVariantCollection $imageVariantCollection)
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
            'imageVariant',
            'string',
            'Image variant to render',
            true
        );
        $this->registerArgument(
            'additionalAttributes',
            'array',
            'Additional tag attributes',
            false,
            []
        );
    }

    public function render(): string
    {
        $pictureTag = new PictureTag(
            imageVariant: $this->imageVariantCollection->get($this->arguments['imageVariant']),
            fileReference: $this->arguments['image'],
            additionalTagAttributes: $this->tag->getAttributes(),
        );
        return $pictureTag->build()->render();
    }
}
