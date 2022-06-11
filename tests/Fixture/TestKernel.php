<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18NBundle\Test\Fixture;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Webmunkeez\I18NBundle\Test\Fixture\TestBundle\TestBundle;
use Webmunkeez\I18NBundle\WebmunkeezI18NBundle;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new WebmunkeezI18NBundle(),
            new TestBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }
}
