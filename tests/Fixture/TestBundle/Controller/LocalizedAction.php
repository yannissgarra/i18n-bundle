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
#[Route(self::FRENCH_ROUTE_URI)]
#[Route(self::API_ROUTE_URI)]
#[Route(self::ENGLISH_ROUTE_URI)]
final class LocalizedAction
{
    public const FRENCH_ROUTE_URI = '/fr/test';
    public const API_ROUTE_URI = '/api/test';
    public const ENGLISH_ROUTE_URI = '/test';

    public function __invoke(): Response
    {
        return new Response();
    }
}
