<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmunkeez\I18nBundle\Exception\SiteNotFoundException;
use Webmunkeez\I18nBundle\Model\LocalizedSite;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteRequestListener
{
    private SiteRepositoryInterface $siteRepository;

    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // acts only if there is at least one site defined
        if (0 === $this->siteRepository->countAll()) {
            return;
        }

        try {
            $site = $this->siteRepository->findOneByUrl($request->getHost(), $request->getRequestUri());

            $request->attributes->set('current-site', $site);

            if ($site instanceof LocalizedSite && null !== $site->getLanguage()) {
                $request->setLocale($site->getLanguage()->getLocale());
                $request->attributes->set('current-language', $site->getLanguage());
            }
        } catch (SiteNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }
}
