<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\EventListener;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LocaleRequestListener
{
    public function __construct(
        private readonly LanguageRepositoryInterface $languageRepository,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        try {
            $locale = $request->query->get('_locale');
        } catch (BadRequestException $e) {
            // `_locale` was a non-scalar value (e.g. `?_locale[]=fr`)
            $locale = null;
        }

        if (false === is_string($locale)) {
            $locale = null;
        }

        if (null !== $locale && true === $this->languageRepository->localeExists($locale)) {
            $language = $this->languageRepository->findOneByLocale($locale);

            $request->setLocale($language->getLocale());
            $request->attributes->set('current-language', $language);

            return;
        }

        if (null !== $request->attributes->get('current-language')) {
            return;
        }

        $language = $this->languageRepository->findOneDefault();

        $request->setLocale($language->getLocale());
        $request->attributes->set('current-language', $language);
    }
}
