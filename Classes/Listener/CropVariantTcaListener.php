<?php

declare(strict_types=1);

namespace Helhum\TopImage\Listener;

use Helhum\TopImage\Definition\TCA;
use Helhum\TopImage\TCA\CropVariantGenerator;
use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;

class CropVariantTcaListener
{
    public function __construct(private readonly CropVariantGenerator $cropVariantGenerator)
    {

    }

    public function addCropVariantsToTca(AfterTcaCompilationEvent $event): void
    {
        $event->setTca(
            $this->cropVariantGenerator->createTca(new TCA($event->getTca()))->get()
        );
    }
}
