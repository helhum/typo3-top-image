<?php

declare(strict_types=1);

namespace Helhum\TopImage\DependencyInjection;

use Helhum\TopImage\Definition\ImageVariantCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class CropVariantProviderPass implements CompilerPassInterface
{
    private string $tagName;

    private DependencyOrderingService $orderer;

    public function __construct(string $tagName)
    {
        $this->tagName = $tagName;
        $this->orderer = new DependencyOrderingService();
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(ImageVariantCollection::class)) {
            // If there's no crop variant generator registered to begin with
            return;
        }
        $imageVariantCollection = $container->findDefinition(ImageVariantCollection::class);

        $unorderedCropVariantServices = $this->collectCropVariantServices($container);
        foreach ($this->orderer->orderByDependencies($unorderedCropVariantServices) as $service) {
            $imageVariantCollection->addArgument($container->findDefinition($service['service']));
        }
    }

    /**
     * @return array<string, array{service: string, before: list<non-empty-string>, after: list<non-empty-string>}>
     */
    private function collectCropVariantServices(ContainerBuilder $container): array
    {
        $unorderedCropVariants = [];
        foreach ($container->findTaggedServiceIds($this->tagName) as $serviceName => $tags) {
            foreach ($tags as $attributes) {
                $cropDefinitionIdentifier = (string)($attributes['identifier'] ?? $serviceName);
                $unorderedCropVariants[$cropDefinitionIdentifier] = [
                    'service' => $serviceName,
                    'before' => GeneralUtility::trimExplode(',', $attributes['before'] ?? '', true),
                    'after' => GeneralUtility::trimExplode(',', $attributes['after'] ?? '', true),
                ];
            }
        }

        return array_merge([], $unorderedCropVariants);
    }
}
