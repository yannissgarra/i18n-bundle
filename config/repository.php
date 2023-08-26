<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webmunkeez\I18nBundle\DependencyInjection\Repository\LanguageDependencyInjectionRepository;
use Webmunkeez\I18nBundle\DependencyInjection\Repository\SiteDependencyInjectionRepository;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(LanguageDependencyInjectionRepository::class)
            ->args([param('webmunkeez_i18n.enabled_locales')])

        ->set(LanguageRepositoryInterface::class)

        ->alias(LanguageRepositoryInterface::class, LanguageDependencyInjectionRepository::class)

        ->set(SiteDependencyInjectionRepository::class)
            ->args([param('webmunkeez_i18n.sites'), service(LanguageRepositoryInterface::class)])

        ->set(SiteRepositoryInterface::class)

        ->alias(SiteRepositoryInterface::class, SiteDependencyInjectionRepository::class);
};
