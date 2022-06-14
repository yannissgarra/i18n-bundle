<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Model;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
interface LanguageAwareInterface extends LocaleAwareInterface
{
    public function getLanguage(): Language;

    public function setLanguage(Language $language): static;
}
