<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Uid\Uuid;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webmunkeez_i18n');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('enabled_locales')
                    ->performNoDeepMerging()
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(fn (string $locale): bool => false === Locales::exists($locale))
                                ->thenInvalid('Invalid locale %s')
                        ->end()
                    ->end() // locale
                ->end() // enabled_locales
                ->scalarNode('default_locale')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(fn (string $locale): bool => false === Locales::exists($locale))
                            ->thenInvalid('Invalid locale %s')
                    ->end()
                ->end() // default_locale
                ->arrayNode('sites')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('id')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                    ->ifTrue(fn (string $id): bool => false === Uuid::isValid($id))
                                        ->thenInvalid('Invalid id format %s')
                                ->end()
                            ->end()
                            ->scalarNode('host')
                                ->cannotBeEmpty()
                                ->defaultValue('localhost')
                            ->end()
                            ->scalarNode('path')
                                ->cannotBeEmpty()
                                ->defaultValue('^\/')
                            ->end()
                            ->scalarNode('locale')
                                ->defaultNull()
                                ->validate()
                                    ->ifTrue(fn (string $locale): bool => false === Locales::exists($locale))
                                        ->thenInvalid('Invalid locale %s')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end() // sites
            ->end()
            ->validate()
                ->ifTrue(fn (array $config): bool => false === in_array($config['default_locale'], $config['enabled_locales']))
                    ->then(fn (array $config): mixed => throw new \InvalidArgumentException(sprintf('Default locale "%s" is not part of enabled locales %s', $config['default_locale'], json_encode($config['enabled_locales']))))
            ->end()
            ->validate()
                ->ifTrue(function (array $config): bool {
                    $isLocaleNotEnabled = false;

                    foreach ($config['sites'] as $site) {
                        if (
                            null !== $site['locale']
                            && false === in_array($site['locale'], $config['enabled_locales'])
                        ) {
                            $isLocaleNotEnabled = true;
                        }
                    }

                    return $isLocaleNotEnabled;
                })
                    ->then(fn (array $config): mixed => throw new \InvalidArgumentException(sprintf('A site locale is not part of enabled locales %s', json_encode($config['enabled_locales']))))
            ->end();

        return $treeBuilder;
    }
}
