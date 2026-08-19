<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller\PostAction;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class PostActionFunctionalTest extends KernelTestCase
{
    private PostAction $action;

    protected function setUp(): void
    {
        $this->action = static::getContainer()->get(PostAction::class);
    }

    public function testInvokeWithAlreadySetLanguageShouldSucceed(): void
    {
        $response = $this->action->__invoke('en', (new Language())->setLocale('fr')->setName('Français'));

        $crawler = new Crawler($response->getContent());

        $this->assertSame('en', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('fr', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('Français', $crawler->filter('p.language span.name')->first()->text());
        $this->assertSame('en', $crawler->filter('p.language-from-locale span.locale')->first()->text()); // no cache
        $this->assertSame('English', $crawler->filter('p.language-from-locale span.name')->first()->text()); // no cache
    }

    public function testInvokeWithExistingLocaleShouldSucceed(): void
    {
        $response = $this->action->__invoke('en');

        $crawler = new Crawler($response->getContent());

        $this->assertSame('en', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('en', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('English', $crawler->filter('p.language span.name')->first()->text());
        $this->assertSame('en', $crawler->filter('p.language-from-locale span.locale')->first()->text());
        $this->assertSame('English', $crawler->filter('p.language-from-locale span.name')->first()->text());
    }

    public function testInvokeWithNotExistingLocaleShouldFail(): void
    {
        $response = $this->action->__invoke('notexistinglocale');

        $crawler = new Crawler($response->getContent());

        $this->assertSame('notexistinglocale', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.name')->first()->text());
        $this->assertSame('', $crawler->filter('p.language-from-locale span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language-from-locale span.name')->first()->text());
    }

    public function testInvokeWithNotEnabledLocaleShouldFail(): void
    {
        $response = $this->action->__invoke('af');

        $crawler = new Crawler($response->getContent());

        $this->assertSame('af', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.name')->first()->text());
        $this->assertSame('', $crawler->filter('p.language-from-locale span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language-from-locale span.name')->first()->text());
    }
}
