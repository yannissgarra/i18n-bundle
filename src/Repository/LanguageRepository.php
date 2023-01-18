<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Repository;

use Symfony\Component\Intl\Languages;
use Symfony\Component\String\UnicodeString;
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18nBundle\Model\Language;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageRepository implements LanguageRepositoryInterface
{
    /**
     * @var array<string, Language>
     */
    private array $languages = [];

    private Language $defaultLanguage;

    public function __construct(array $enabledLocales, string $defaultLocale)
    {
        foreach ($enabledLocales as $enabledLocale) {
            $this->languages[$enabledLocale] = (new Language())
                ->setLocale($enabledLocale)
                ->setName((new UnicodeString(Languages::getName($enabledLocale, $enabledLocale)))->title()->toString());
        }

        $this->defaultLanguage = (new Language())
            ->setLocale($defaultLocale)
            ->setName((new UnicodeString(Languages::getName($defaultLocale, $defaultLocale)))->title()->toString());
    }

    public function findAll(): array
    {
        return array_values($this->languages);
    }

    public function findOneByLocale(string $locale): Language
    {
        if (false === isset($this->languages[$locale])) {
            throw new LanguageNotFoundException();
        }

        return $this->languages[$locale];
    }

    public function findOneDefault(): Language
    {
        return $this->defaultLanguage;
    }

    public function localeExists(string $locale): bool
    {
        return isset($this->languages[$locale]);
    }
}
