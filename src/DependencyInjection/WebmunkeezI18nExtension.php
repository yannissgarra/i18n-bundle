<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Webmunkeez\I18nBundle\Translation\TranslatorAwareInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class WebmunkeezI18nExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'), $container->getParameter('kernel.environment'));
        $loader->load('event_listener.php');
        $loader->load('repository.php');
        $loader->load('serializer.php');
        $loader->load('twig.php');
        $loader->load('validator.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('webmunkeez_i18n.enabled_locales', $config['enabled_locales']);
        $container->setParameter('webmunkeez_i18n.sites', $config['sites']);

        $container->registerForAutoconfiguration(TranslatorAwareInterface::class)
            ->addMethodCall('setTranslator', [new Reference('translator')]);
    }

    public function prepend(ContainerBuilder $container): void
    {
        // define default config for translation
        $container->prependExtensionConfig('framework', [
            'default_locale' => 'en',
            'translator' => [
                'default_path' => '%kernel.project_dir%/translations',
                'fallbacks' => ['en'],
            ],
            'set_content_language_from_locale' => true,
        ]);
    }
}
