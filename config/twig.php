<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Twig\DateTimeExtension;
use Webmunkeez\I18nBundle\Twig\LanguageAwareExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set(LanguageAwareExtension::class)
            ->args([service(LanguageRepositoryInterface::class)])
            ->tag('twig.extension')

        ->set(DateTimeExtension::class)
            ->args([service('translator')])
            ->tag('twig.extension');
};
