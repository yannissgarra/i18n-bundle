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
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller\TestAction;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestActionFunctionalTest extends KernelTestCase
{
    private TestAction $action;

    protected function setUp(): void
    {
        $this->action = static::getContainer()->get(TestAction::class);
    }

    public function testInvokeWithAlreadySetLanguageShouldSucceed(): void
    {
        $response = $this->action->__invoke('en', (new Language())->setLocale('fr')->setName('Français'));

        $crawler = new Crawler($response->getContent());

        $this->assertSame('en', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('fr', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('Français', $crawler->filter('p.language span.name')->first()->text());
    }

    public function testInvokeWithExistingLocaleShouldSucceed(): void
    {
        $response = $this->action->__invoke('en');

        $crawler = new Crawler($response->getContent());

        $this->assertSame('en', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('en', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('English', $crawler->filter('p.language span.name')->first()->text());
    }

    public function testInvokeWithNotExistingLocaleShouldFail(): void
    {
        $response = $this->action->__invoke('notexistinglocale');

        $crawler = new Crawler($response->getContent());

        $this->assertSame('notexistinglocale', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.name')->first()->text());
    }

    public function testInvokeWithNotEnabledLocaleShouldFail(): void
    {
        $response = $this->action->__invoke('it');

        $crawler = new Crawler($response->getContent());

        $this->assertSame('it', $crawler->filter('p.locale span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.locale')->first()->text());
        $this->assertSame('', $crawler->filter('p.language span.name')->first()->text());
    }
}
