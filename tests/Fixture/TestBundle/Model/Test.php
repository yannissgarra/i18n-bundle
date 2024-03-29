<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model;

use Webmunkeez\I18nBundle\Exception\TranslationNotFoundException;
use Webmunkeez\I18nBundle\Model\TranslationAwareInterface;
use Webmunkeez\I18nBundle\Model\TranslationInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class Test implements TranslationAwareInterface
{
    private array $translations;

    public function getTranslations(): iterable
    {
        return $this->translations;
    }

    public function getTranslation(string $locale): TranslationInterface
    {
        $translations = array_filter($this->translations, fn (TestTranslation $translation): bool => $locale === $translation->getLocale());

        if (1 !== count($translations)) {
            throw new TranslationNotFoundException();
        }

        return $translations[0];
    }

    public function addTranslation(TranslationInterface $translation): self
    {
        $this->translations[] = $translation;

        return $this;
    }
}
