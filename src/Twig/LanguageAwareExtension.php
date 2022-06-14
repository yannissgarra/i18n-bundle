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
    private LanguageRepositoryInterface $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('language', [$this, 'getLanguage']),
        ];
    }

    public function getLanguage(LanguageAwareInterface $object): ?Language
    {
        try {
            return $this->languageRepository->findOneByLocale($object->getLocale());
        } catch (LanguageNotFoundException $e) {
            return null;
        }
    }
}
