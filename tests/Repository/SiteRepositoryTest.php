<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Repository;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Webmunkeez\I18nBundle\Exception\SiteNotFoundException;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Model\LocalizedSite;
use Webmunkeez\I18nBundle\Model\Site;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Repository\SiteRepository;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteRepositoryTest extends TestCase
{
    public const DATA = [
        'french' => [
            'id' => '831ee06a-63b5-41ee-8506-4b75dea2f7cf',
            'host' => 'example.com',
            'path' => '^\/fr',
            'locale' => 'fr',
            'language' => [
                'locale' => 'fr',
                'name' => 'Français',
            ],
        ],
        'api' => [
            'id' => '5844634b-e43a-4fa1-8e74-1e213ff3a90d',
            'host' => 'example.com',
            'path' => '^\/api',
            'locale' => null,
        ],
        'english' => [
            'id' => '5e9c8b25-fa50-4964-907a-e53adefa5729',
            'host' => 'example.com',
            'path' => '^\/',
            'locale' => 'en',
            'language' => [
                'locale' => 'en',
                'name' => 'English',
            ],
        ],
        'spanish' => [
            'id' => '2a3cabe4-d105-4dd5-9c73-8300f977cc06',
            'host' => 'es.example.com',
            'path' => '^\/',
            'locale' => 'es',
            'language' => [
                'locale' => 'es',
                'name' => 'Español',
            ],
        ],
    ];

    /**
     * @var LanguageRepositoryInterface&MockObject
     **/
    private LanguageRepositoryInterface $languageRepository;

    private SiteRepositoryInterface $siteRepository;

    protected function setUp(): void
    {
        /** @var LanguageRepositoryInterface&MockObject $languageRepository */
        $languageRepository = $this->getMockBuilder(LanguageRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->languageRepository = $languageRepository;

        $this->languageRepository->expects($this->exactly(3))->method('findOneByLocale')
            ->withConsecutive([self::DATA['french']['locale']], [self::DATA['english']['locale']])
            ->willReturnOnConsecutiveCalls(
                (new Language())->setLocale(self::DATA['french']['language']['locale'])->setName(self::DATA['french']['language']['name']),
                (new Language())->setLocale(self::DATA['english']['language']['locale'])->setName(self::DATA['english']['language']['name']),
                (new Language())->setLocale(self::DATA['spanish']['language']['locale'])->setName(self::DATA['spanish']['language']['name'])
            );

        $this->siteRepository = new SiteRepository(array_values(self::DATA), $this->languageRepository);
    }

    public function testFindAllShouldSucceed(): void
    {
        $sites = $this->siteRepository->findAll();

        $this->assertCount(4, $sites);
        $this->assertInstanceOf(LocalizedSite::class, $sites[0]);
        $this->assertTrue($sites[0]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['french']['id'])));
        $this->assertSame(self::DATA['french']['host'], $sites[0]->getHost());
        $this->assertSame(self::DATA['french']['path'], $sites[0]->getPath());
        $this->assertSame(self::DATA['french']['locale'], $sites[0]->getLocale());
        $this->assertSame(self::DATA['french']['language']['locale'], $sites[0]->getLanguage()->getLocale());
        $this->assertSame(self::DATA['french']['language']['name'], $sites[0]->getLanguage()->getName());
        $this->assertInstanceOf(Site::class, $sites[1]);
        $this->assertTrue($sites[1]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['api']['id'])));
        $this->assertSame(self::DATA['api']['host'], $sites[1]->getHost());
        $this->assertSame(self::DATA['api']['path'], $sites[1]->getPath());
        $this->assertInstanceOf(LocalizedSite::class, $sites[2]);
        $this->assertTrue($sites[2]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(self::DATA['english']['host'], $sites[2]->getHost());
        $this->assertSame(self::DATA['english']['path'], $sites[2]->getPath());
        $this->assertSame(self::DATA['english']['locale'], $sites[2]->getLocale());
        $this->assertSame(self::DATA['english']['language']['locale'], $sites[2]->getLanguage()->getLocale());
        $this->assertSame(self::DATA['english']['language']['name'], $sites[2]->getLanguage()->getName());
        $this->assertInstanceOf(LocalizedSite::class, $sites[3]);
        $this->assertTrue($sites[3]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['spanish']['id'])));
        $this->assertSame(self::DATA['spanish']['host'], $sites[3]->getHost());
        $this->assertSame(self::DATA['spanish']['path'], $sites[3]->getPath());
        $this->assertSame(self::DATA['spanish']['locale'], $sites[3]->getLocale());
        $this->assertSame(self::DATA['spanish']['language']['locale'], $sites[3]->getLanguage()->getLocale());
        $this->assertSame(self::DATA['spanish']['language']['name'], $sites[3]->getLanguage()->getName());
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
        $this->assertSame(self::DATA['french']['host'], $sites[0]->getHost());
        $this->assertSame(self::DATA['french']['path'], $sites[0]->getPath());
        $this->assertSame(self::DATA['french']['locale'], $sites[0]->getLocale());
        $this->assertSame(self::DATA['french']['language']['locale'], $sites[0]->getLanguage()->getLocale());
        $this->assertSame(self::DATA['french']['language']['name'], $sites[0]->getLanguage()->getName());
        $this->assertInstanceOf(LocalizedSite::class, $sites[1]);
        $this->assertTrue($sites[1]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(self::DATA['english']['host'], $sites[1]->getHost());
        $this->assertSame(self::DATA['english']['path'], $sites[1]->getPath());
        $this->assertSame(self::DATA['english']['locale'], $sites[1]->getLocale());
        $this->assertSame(self::DATA['english']['language']['locale'], $sites[1]->getLanguage()->getLocale());
        $this->assertSame(self::DATA['english']['language']['name'], $sites[1]->getLanguage()->getName());
        $this->assertInstanceOf(LocalizedSite::class, $sites[2]);
        $this->assertTrue($sites[2]->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['spanish']['id'])));
        $this->assertSame(self::DATA['spanish']['host'], $sites[2]->getHost());
        $this->assertSame(self::DATA['spanish']['path'], $sites[2]->getPath());
        $this->assertSame(self::DATA['spanish']['locale'], $sites[2]->getLocale());
        $this->assertSame(self::DATA['spanish']['language']['locale'], $sites[2]->getLanguage()->getLocale());
        $this->assertSame(self::DATA['spanish']['language']['name'], $sites[2]->getLanguage()->getName());
    }

    public function testFindOneByUrlWithoutPathShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByUrl('example.com', '/test');

        $this->assertInstanceOf(LocalizedSite::class, $site);
        $this->assertTrue($site->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(self::DATA['english']['host'], $site->getHost());
        $this->assertSame(self::DATA['english']['path'], $site->getPath());
        $this->assertSame(self::DATA['english']['locale'], $site->getLocale());
        $this->assertSame(self::DATA['english']['language']['locale'], $site->getLanguage()->getLocale());
        $this->assertSame(self::DATA['english']['language']['name'], $site->getLanguage()->getName());
    }

    public function testFindOneByUrlWithPathShouldSucceed(): void
    {
        $site = $this->siteRepository->findOneByUrl('example.com', '/api/test');

        $this->assertInstanceOf(Site::class, $site);
        $this->assertTrue($site->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['api']['id'])));
        $this->assertSame(self::DATA['api']['host'], $site->getHost());
        $this->assertSame(self::DATA['api']['path'], $site->getPath());
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
        $this->assertTrue($site->getId()->equals(Uuid::fromString(SiteRepositoryTest::DATA['english']['id'])));
        $this->assertSame(self::DATA['english']['host'], $site->getHost());
        $this->assertSame(self::DATA['english']['path'], $site->getPath());
        $this->assertSame(self::DATA['english']['locale'], $site->getLocale());
        $this->assertSame(self::DATA['english']['language']['locale'], $site->getLanguage()->getLocale());
        $this->assertSame(self::DATA['english']['language']['name'], $site->getLanguage()->getName());
    }

    public function testFindOneByLocaleWithWrongLocaleShouldFail(): void
    {
        $this->expectException(SiteNotFoundException::class);

        $this->siteRepository->findOneByLocale('notexisting');
    }
}
