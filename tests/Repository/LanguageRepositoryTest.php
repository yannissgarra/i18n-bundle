<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Repository;

use PHPUnit\Framework\TestCase;
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18NBundle\Model\Language;
use Webmunkeez\I18nBundle\Repository\LanguageRepository;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageRepositoryTest extends TestCase
{
    private LanguageRepositoryInterface $languageRepository;

    protected function setUp(): void
    {
        $this->languageRepository = new LanguageRepository(['en', 'fr'], 'en');
    }

    public function testFindAllShouldSucceed(): void
    {
        $languages = $this->languageRepository->findAll();

        $this->assertCount(2, $languages);
        $this->assertSame('en', $languages[0]->getLocale());
        $this->assertSame('English', $languages[0]->getName());
        $this->assertSame('fr', $languages[1]->getLocale());
        $this->assertSame('Français', $languages[1]->getName());
    }

    public function testFindOneByLocaleShouldSucceed(): void
    {
        $language = $this->languageRepository->findOneByLocale('en');

        $this->assertInstanceOf(Language::class, $language);
        $this->assertSame('en', $language->getLocale());
        $this->assertSame('English', $language->getName());
    }

    public function testFindOneByLocaleWithWrongLocaleShouldFail(): void
    {
        $this->expectException(LanguageNotFoundException::class);

        $this->languageRepository->findOneByLocale('notexisting');
    }

    public function testLocaleExistsShouldSucceed(): void
    {
        $this->assertTrue($this->languageRepository->localeExists('en'));
    }

    public function testLocaleExistsWithWrongLocaleShouldFail(): void
    {
        $this->assertFalse($this->languageRepository->localeExists('es'));
    }

    public function testFindOneDefaultShouldSucceed(): void
    {
        $language = $this->languageRepository->findOneDefault();

        $this->assertInstanceOf(Language::class, $language);
        $this->assertSame('en', $language->getLocale());
        $this->assertSame('English', $language->getName());
    }
}
