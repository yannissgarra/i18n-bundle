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
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->scalarPrototype()
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->validate()
                            ->ifTrue(fn (string $locale) => false === Locales::exists($locale))
                                ->thenInvalid('Invalid locale %s')
                        ->end()
                    ->end() // locale
                ->end() // enabled_locales
                ->scalarNode('default_locale')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(fn (string $locale) => false === Locales::exists($locale))
                            ->thenInvalid('Invalid locale %s')
                    ->end()
                ->end() // default_locale
            ->end()
            ->validate()
                ->ifTrue(fn (array $config) => false === in_array($config['default_locale'], $config['enabled_locales']))
                    ->then(fn (array $config) => throw new \InvalidArgumentException(sprintf('Default locale "%s" is not part of enabled locales %s', $config['default_locale'], json_encode($config['enabled_locales']))))
            ->end();

        return $treeBuilder;
    }
}
