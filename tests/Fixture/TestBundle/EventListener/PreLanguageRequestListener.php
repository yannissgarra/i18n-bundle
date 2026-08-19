<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Fixture\TestBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller\LocalizedAction;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class PreLanguageRequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (LocalizedAction::API_ROUTE_URI_2 === $request->getPathInfo()) {
            $language = (new Language())->setLocale('fr')->setName('Français');

            $request->setLocale($language->getLocale());
            $request->attributes->set('current-language', $language);
        }
    }
}
