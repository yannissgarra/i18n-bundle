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

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(LanguageRepository::class)
            ->args([param('webmunkeez_i18n.languages')])

        ->set(LanguageRepositoryInterface::class)

        ->alias(LanguageRepositoryInterface::class, LanguageRepository::class);
};
