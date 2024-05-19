<?php

declare(strict_types=1);

namespace Helhum\TopImage;

use Helhum\TopImage\TCA\ImageManipulationConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(ImageManipulationConfigurationInterface::class)->addTag('top.image.crop.variants');

    $containerBuilder->addCompilerPass(new DependencyInjection\CropVariantProviderPass('top.image.crop.variants'));
};
