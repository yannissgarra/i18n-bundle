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
final class LanguageRepository implements LanguageRepositoryInterface
{
    /**
     * @var array<string, Language>
     */
    private array $languages = [];

    public function __construct(array $languagesData)
    {
        foreach ($languagesData as $languageData) {
            $this->languages[$languageData['locale']] = (new Language())
                ->setLocale($languageData['locale'])
                ->setName($languageData['name']);
        }
    }

    public function findAll(): array
    {
        return array_values($this->languages);
    }

    public function findOneByLocale(string $locale): Language
    {
        if (false === array_key_exists($locale, $this->languages)) {
            throw new LanguageNotFoundException();
        }

        return $this->languages[$locale];
    }

    public function localeExists(string $locale): bool
    {
        return array_key_exists($locale, $this->languages);
    }
}
