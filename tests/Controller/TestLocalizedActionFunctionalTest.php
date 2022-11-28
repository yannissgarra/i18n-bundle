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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller\LocalizedAction;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestLocalizedActionFunctionalTest extends WebTestCase
{
    public function testFrenchRouteUrlShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::FRENCH_ROUTE_URI);

        $this->assertResponseIsSuccessful();
        $this->assertSame('fr', $client->getResponse()->headers->get('content-language'));
    }

    public function testApiRouteUrlWithLocaleShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::API_ROUTE_URI, ['_locale' => 'fr']);

        $this->assertResponseIsSuccessful();
        $this->assertSame('fr', $client->getResponse()->headers->get('content-language'));
    }

    public function testApiRouteUrlWithoutLocaleShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::API_ROUTE_URI);

        $this->assertResponseIsSuccessful();
        $this->assertSame('en', $client->getResponse()->headers->get('content-language'));
    }

    public function testEnglishRouteUrlShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::ENGLISH_ROUTE_URI);

        $this->assertResponseIsSuccessful();
        $this->assertSame('en', $client->getResponse()->headers->get('content-language'));
    }

    public function testSpanishRouteUrlShouldSucceed(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'es.example.com']);
        $client->request(Request::METHOD_GET, LocalizedAction::SPANISH_ROUTE_URI);

        $this->assertResponseIsSuccessful();
        $this->assertSame('es', $client->getResponse()->headers->get('content-language'));
    }

    public function testSpanishRouteUriAndEnglishRouteHostShouldThrowException(): void
    {
        $client = static::createClient([], ['HTTP_HOST' => 'example.com']);
        $client->catchExceptions(false);

        $this->expectException(NotFoundHttpException::class);

        $client->request(Request::METHOD_GET, LocalizedAction::SPANISH_ROUTE_URI);
    }
}
