<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Service;

use Webmunkeez\I18nBundle\Translation\TranslatorAwareInterface;
use Webmunkeez\I18nBundle\Translation\TranslatorAwareTrait;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestService implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;
}
