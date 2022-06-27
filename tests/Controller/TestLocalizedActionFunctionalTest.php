<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller\LocalizedAction;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestLocalizedActionFunctionalTest extends WebTestCase
{
    public function testFrenchRouteUriShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::FRENCH_ROUTE_URI);

        $this->assertSame('fr', $client->getResponse()->headers->get('content-language'));
    }

    public function testApiRouteUriWithLocaleShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::API_ROUTE_URI, ['_locale' => 'fr']);

        $this->assertSame('fr', $client->getResponse()->headers->get('content-language'));
    }

    public function testApiRouteUriWithoutLocaleShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::API_ROUTE_URI);

        $this->assertSame('en', $client->getResponse()->headers->get('content-language'));
    }

    public function testEnglishRouteUriShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::ENGLISH_ROUTE_URI);

        $this->assertSame('en', $client->getResponse()->headers->get('content-language'));
    }
}
