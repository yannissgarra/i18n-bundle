<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Model\LanguageAwareInterface;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageAwareExtension extends AbstractExtension
{
    public function __construct(
        private readonly LanguageRepositoryInterface $languageRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('language', [$this, 'getLanguage']),
        ];
    }

    public function getLanguage(LanguageAwareInterface|string $localeInfo): ?Language
    {
        if (true === is_string($localeInfo)) {
            try {
                return $this->languageRepository->findOneByLocale($localeInfo);
            } catch (LanguageNotFoundException $e) {
            }

            return null;
        }

        if (null === $localeInfo->getLanguage()) {
            try {
                $localeInfo->setLanguage($this->languageRepository->findOneByLocale($localeInfo->getLocale()));
            } catch (LanguageNotFoundException $e) {
                $localeInfo->setLanguage(null);
            }
        }

        return $localeInfo->getLanguage();
    }
}
