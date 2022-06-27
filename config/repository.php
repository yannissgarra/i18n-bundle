<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webmunkeez\I18nBundle\Repository\LanguageRepository;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Repository\SiteRepository;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(LanguageRepository::class)
            ->args([param('webmunkeez_i18n.enabled_locales'), param('webmunkeez_i18n.default_locale')])

        ->set(LanguageRepositoryInterface::class)

        ->alias(LanguageRepositoryInterface::class, LanguageRepository::class)

        ->set(SiteRepository::class)
            ->args([param('webmunkeez_i18n.sites')], [service(LanguageRepository::class)])

        ->set(SiteRepositoryInterface::class)

        ->alias(SiteRepositoryInterface::class, SiteRepository::class);
};
