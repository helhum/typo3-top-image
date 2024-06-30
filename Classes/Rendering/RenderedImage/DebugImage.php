<?php

declare(strict_types=1);

namespace Helhum\TopImage\Rendering\RenderedImage;

use Helhum\TopImage\Definition\ImageFormat;
use Helhum\TopImage\Definition\ImageSource;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Imaging\GifBuilder;

class DebugImage
{
    public function __construct(
        private readonly ProcessedFile $processedFile,
        private readonly ImageSource|ImageSource\FallbackSource $source,
        private readonly ?ImageFormat $format = null,
        private readonly ?string $imageVariant = null,
    ) {
    }

    public function getPublicUrl(): string
    {
        if (($GLOBALS['TYPO3_CONF_VARS']['extConf']['top_image']['debug'] ?? false) === true) {
            return $this->createDebugImage();
        }
        return (string)$this->processedFile->getPublicUrl();
    }

    private function createDebugImage(): string
    {
        $width = $this->processedFile->getProperty('width');
        $height = $this->processedFile->getProperty('height');
        $fontSize = max(26 * (max($width, $height) / 800), 26);
        $cropVariant = $this->source instanceof ImageSource\FallbackSource ? $this->source->cropVariant : $this->source->artDirection?->cropVariant;
        $sizes = $this->source instanceof ImageSource\FallbackSource ? null : $this->source->sizes;

        $textLines = [];
        $textLines[] = sprintf('image variant: %s', $this->imageVariant ?? 'invalid');
        $textLines[] = sprintf('size: %s', implode('×', [$width, $height]));
        $textLines[] = sprintf('format: %s', $this->format?->value ?? 'jpg');
        $textLines[] = sprintf('sizes: %s', implode(', ', $sizes ?? ['none']));
        $textLines[] = sprintf('media: %s', $this->source->artDirection?->media ?? 'none');
        $textLines[] = sprintf('crop variant: %s', $cropVariant ?? 'none');

        $gifBuilder = GeneralUtility::makeInstance(GifBuilder::class);
        $config = [
            'XY' => implode(',', [$width, $height]),
            'backColor' => '#C0C0C0',
            // 'format' => $this->format?->value ?? 'jpg', @todo: find out why rendering other formats results in unreadable text
            'format' => 'jpg',
        ];
        foreach ($textLines as $index => $line) {
            $key = sprintf('%d0', $index + 1);
            $config[$key] = 'TEXT';
            $config[$key . '.'] = [
                'text' => $line,
                'fontColor' => '#000000',
                'fontSize' => $fontSize,
                'antiAlias' => false,
                'align' => 'center',
                'offset' => implode(',', [0, (($height / 2) - (count($textLines) * $fontSize / 2)) + ($index + 1) * $fontSize]),
            ];
        }
        $gifBuilder->start(
            $config,
            [],
        );

        return '/' . $gifBuilder->gifBuild();
    }
}
