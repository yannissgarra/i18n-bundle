<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Model;

use Webmunkeez\I18nBundle\Exception\TranslationNotFoundException;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
interface TranslationAwareInterface
{
    /**
     * @return iterable<TranslationInterface>
     */
    public function getTranslations(): iterable;

    /**
     * @throws TranslationNotFoundException
     */
    public function getTranslation(string $locale): TranslationInterface;
}
