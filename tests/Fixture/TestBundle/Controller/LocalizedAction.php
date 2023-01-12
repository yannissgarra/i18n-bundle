<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
#[Route(self::FRENCH_ROUTE_URI, condition: "request.getLocale() === 'fr'")]
#[Route(self::API_ROUTE_URI)]
#[Route(self::ENGLISH_ROUTE_URI, condition: "request.getLocale() === 'en'")]
#[Route(self::SPANISH_ROUTE_URI, condition: "request.getLocale() === 'es'")]
final class LocalizedAction
{
    public const FRENCH_ROUTE_URI = '/fr/un-test';

    public const API_ROUTE_URI = '/api/a-test';

    public const ENGLISH_ROUTE_URI = '/a-test';

    public const SPANISH_ROUTE_URI = '/una-prueba';

    public function __invoke(): Response
    {
        return new Response();
    }
}
