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
use Symfony\Component\Uid\Uuid;
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

        $this->assertCount(4, $sites);
        $this->assertInstanceOf(LocalizedSite::class, $sites[0]);
        $this->assertTrue($sites[0]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['french']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['french']['host'], $sites[0]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['french']['path'], $sites[0]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['french']['locale'], $sites[0]->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['french']['language']['locale'], $sites[0]->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['french']['language']['name'], $sites[0]->getLanguage()->getName());
        $this->assertInstanceOf(Site::class, $sites[1]);
        $this->assertTrue($sites[1]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['api']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['api']['host'], $sites[1]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['api']['path'], $sites[1]->getPath());
        $this->assertInstanceOf(LocalizedSite::class, $sites[2]);
        $this->assertTrue($sites[2]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $sites[2]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $sites[2]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $sites[2]->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['english']['language']['locale'], $sites[2]->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['english']['language']['name'], $sites[2]->getLanguage()->getName());
        $this->assertInstanceOf(LocalizedSite::class, $sites[3]);
        $this->assertTrue($sites[3]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['spanish']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['host'], $sites[3]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['path'], $sites[3]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['locale'], $sites[3]->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['language']['locale'], $sites[3]->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['language']['name'], $sites[3]->getLanguage()->getName());
    }

    public function testCountAllShouldSucceed(): void
    {
        $counter = $this->siteRepository->countAll();

        $this->assertSame(4, $counter);
    }

    public function testFindAllLocalizedShouldSucceed(): void
    {
        $sites = $this->siteRepository->findAllLocalized();

        $this->assertCount(3, $sites);
        $this->assertInstanceOf(LocalizedSite::class, $sites[0]);
        $this->assertTrue($sites[0]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['french']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['french']['host'], $sites[0]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['french']['path'], $sites[0]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['french']['locale'], $sites[0]->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['french']['language']['locale'], $sites[0]->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['french']['language']['name'], $sites[0]->getLanguage()->getName());
        $this->assertInstanceOf(LocalizedSite::class, $sites[1]);
        $this->assertTrue($sites[1]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $sites[1]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $sites[1]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $sites[1]->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['english']['language']['locale'], $sites[1]->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['english']['language']['name'], $sites[1]->getLanguage()->getName());
        $this->assertInstanceOf(LocalizedSite::class, $sites[2]);
        $this->assertTrue($sites[2]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['spanish']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['host'], $sites[2]->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['path'], $sites[2]->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['locale'], $sites[2]->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['language']['locale'], $sites[2]->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['spanish']['language']['name'], $sites[2]->getLanguage()->getName());
    }

    public function testFindOneByUrlWithoutPathShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByUrl('example.com', '/test');

        $this->assertInstanceOf(LocalizedSite::class, $site);
        $this->assertTrue($site->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['english']['host'], $site->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['english']['path'], $site->getPath());
        $this->assertSame(SiteRepositoryTest::DATA['english']['locale'], $site->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['english']['language']['locale'], $site->getLanguage()->getLocale());
        $this->assertSame(SiteRepositoryTest::DATA['english']['language']['name'], $site->getLanguage()->getName());
    }

    public function testFindOneByUrlWithPathShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByUrl('example.com', '/api/test');

        $this->assertInstanceOf(Site::class, $site);
        $this->assertTrue($site->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['api']['id'])));
        $this->assertSame(SiteRepositoryTest::DATA['api']['host'], $site->getHost());
        $this->assertSame(SiteRepositoryTest::DATA['api']['path'], $site->getPath());
    }

    public function testFindOneByUrlWithNotExistingHostShouldFail(): void
    {
        $this->expectException(SiteNotFoundException::class);

        $this->siteRepository->findOneByUrl('_example.com', '/test');
    }
}
