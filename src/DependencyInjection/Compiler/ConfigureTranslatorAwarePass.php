<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class ConfigureTranslatorAwarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach (array_keys($container->findTaggedServiceIds('webmunkeez_i18n.translator_aware')) as $serviceId) {
            $definition = $container->findDefinition($serviceId);

            if (false === $definition->hasMethodCall('setTranslator')) {
                $definition->addMethodCall('setTranslator', [new Reference('translator')]);
            }
        }
    }
}
