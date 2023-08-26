<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webmunkeez\I18nBundle\EventListener\LocaleRequestListener;
use Webmunkeez\I18nBundle\EventListener\SiteRequestListener;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

return function (ContainerConfigurator $container) {
    $container->services()
        ->set(SiteRequestListener::class)
            ->args([service(SiteRepositoryInterface::class)])
            ->tag('kernel.event_listener', ['event' => 'kernel.request', 'priority' => 50])

        ->set(LocaleRequestListener::class)
            ->args([service(LanguageRepositoryInterface::class)])
            ->tag('kernel.event_listener', ['event' => 'kernel.request', 'priority' => 40]);
};
