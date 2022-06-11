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
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18NBundle\Model\Language;
use Webmunkeez\I18nBundle\Repository\LanguageRepository;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageRepositoryFunctionalTest extends KernelTestCase
{
    private LanguageRepositoryInterface $languageRepository;

    protected function setUp(): void
    {
        $this->languageRepository = static::getContainer()->get(LanguageRepository::class);
    }

    public function testFindAllShouldSucceed(): void
    {
        $this->assertCount(2, $this->languageRepository->findAll());
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
}
