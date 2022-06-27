<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Webmunkeez\I18nBundle\Exception\SiteNotFoundException;
use Webmunkeez\I18nBundle\Model\LocalizedSite;
use Webmunkeez\I18nBundle\Model\Site;
use Webmunkeez\I18nBundle\Repository\SiteRepository;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteRepositoryFunctionalTest extends KernelTestCase
{
    private SiteRepositoryInterface $siteRepository;

    protected function setUp(): void
    {
        $this->siteRepository = static::getContainer()->get(SiteRepository::class);
    }

    public function testFindAllShouldSucceed(): void
    {
        $sites = $this->siteRepository->findAll();

        $this->assertCount(3, $sites);
        $this->assertInstanceOf(LocalizedSite::class, $sites[0]);
        $this->assertSame(SiteRepositoryTest::DATA['french']['id'], $sites[0]->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['french']['host'], $sites[0]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['french']['path'], $sites[0]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['french']['locale'], $sites[0]->getLocale());
        $this->assertInstanceOf(Site::class, $sites[1]);
        $this->assertSame(SiteRepositoryTest::DATA['api']['id'], $sites[1]->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['api']['host'], $sites[1]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['api']['path'], $sites[1]->getPath());
        $this->assertInstanceOf(LocalizedSite::class, $sites[2]);
        $this->assertSame(SiteRepositoryTest::DATA['english']['id'], $sites[2]->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $sites[2]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $sites[2]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $sites[2]->getLocale());
    }

    public function testFindAllLocalizedShouldSucceed(): void
    {
        $sites = $this->siteRepository->findAllLocalized();

        $this->assertCount(2, $sites);
        $this->assertInstanceOf(LocalizedSite::class, $sites[0]);
        $this->assertSame(SiteRepositoryTest::DATA['french']['id'], $sites[0]->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['french']['host'], $sites[0]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['french']['path'], $sites[0]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['french']['locale'], $sites[0]->getLocale());
        $this->assertInstanceOf(LocalizedSite::class, $sites[1]);
        $this->assertSame(SiteRepositoryTest::DATA['english']['id'], $sites[1]->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $sites[1]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $sites[1]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $sites[1]->getLocale());
    }

    public function testFindOneByUrlWithoutPathShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByUrl('example.com', '/test');

        $this->assertInstanceOf(LocalizedSite::class, $site);
        $this->assertSame(SiteRepositoryTest::DATA['english']['id'], $site->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $site->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $site->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $site->getLocale());
    }

    public function testFindOneByUrlWithPathShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByUrl('example.com', '/api/test');

        $this->assertInstanceOf(Site::class, $site);
        $this->assertSame(SiteRepositoryTest::DATA['api']['id'], $site->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['api']['host'], $site->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['api']['path'], $site->getPath());
    }

    public function testFindOneByUrlWithNotExistingHostShouldFail(): void
    {
        $this->expectException(SiteNotFoundException::class);

        $this->siteRepository->findOneByUrl('_example.com', '/test');
    }

    public function testFindOneByLocaleShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByLocale('en');

        $this->assertInstanceOf(LocalizedSite::class, $site);
        $this->assertSame(SiteRepositoryTest::DATA['english']['id'], $site->getId()->toRfc4122());
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $site->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $site->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $site->getLocale());
    }

    public function testFindOneByLocaleWithWrongLocaleShouldFail(): void
    {
        $this->expectException(SiteNotFoundException::class);

        $this->siteRepository->findOneByLocale('notexisting');
    }
}
