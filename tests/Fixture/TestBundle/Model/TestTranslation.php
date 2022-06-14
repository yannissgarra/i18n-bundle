<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model;

use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Model\LanguageAwareInterface;
use Webmunkeez\I18nBundle\Model\TranslationInterface;
use Webmunkeez\I18nBundle\Validator\Constraint\Locale;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestTranslation implements TranslationInterface, LanguageAwareInterface
{
    #[Locale]
    private string $locale;

    private Language $language;

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): static
    {
        $this->language = $language;

        return $this;
    }
}
