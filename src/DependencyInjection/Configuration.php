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
use Symfony\Component\Intl\Languages;
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
                ->arrayNode('languages')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('locale')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                    ->ifTrue(fn ($locale) => false === Locales::exists($locale))
                                    ->thenInvalid('Invalid language locale %s')
                                ->end()
                            ->end() // locale
                            ->scalarNode('name')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end() // name
                        ->end()
                    ->end() // language
                ->end() // languages
            ->end();

        return $treeBuilder;
    }
}
