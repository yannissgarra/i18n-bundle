<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Repository;

use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18nBundle\Model\Language;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
interface LanguageRepositoryInterface
{
    /**
     * @return array<Language>
     */
    public function findAll(): array;

    /**
     * @throws LanguageNotFoundException
     */
    public function findOneByLocale(string $locale): Language;

    public function localeExists(string $locale): bool;
}
