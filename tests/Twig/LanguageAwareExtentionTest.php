<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Twig;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\TestTranslation;
use Webmunkeez\I18nBundle\Twig\LanguageAwareExtension;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageAwareExtensionTest extends TestCase
{
    /**
     * @var LanguageRepositoryInterface&MockObject
     **/
    private LanguageRepositoryInterface $languageRepository;

    private LanguageAwareExtension $extension;

    protected function setUp(): void
    {
        /** @var LanguageRepositoryInterface&MockObject $languageRepository */
        $languageRepository = $this->getMockBuilder(LanguageRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->languageRepository = $languageRepository;

        $this->extension = new LanguageAwareExtension($this->languageRepository);
    }

    public function testGetLanguageWithAlreadySetLanguageShouldSucceed(): void
    {
        $this->languageRepository->method('findOneByLocale')->willReturn((new Language())->setLocale('en')->setName('English'));

        $translation = (new TestTranslation())
            ->setLocale('en') // force test that language repository is not called
            ->setLanguage((new Language())->setLocale('fr')->setName('Français'));

        $language = $this->extension->getLanguage($translation);

        $this->assertInstanceOf(Language::class, $language);
        $this->assertSame('fr', $language->getLocale());
        $this->assertSame('Français', $language->getName());
    }

    public function testGetLanguageWithExistingLocaleShouldSucceed(): void
    {
        $this->languageRepository->method('findOneByLocale')->willReturn((new Language())->setLocale('en')->setName('English'));

        $translation = (new TestTranslation())->setLocale('en');

        $language = $this->extension->getLanguage($translation);

        $this->assertInstanceOf(Language::class, $language);
        $this->assertSame('en', $language->getLocale());
        $this->assertSame('English', $language->getName());
    }

    public function testGetLanguageWithNotExistingLocaleShouldFail(): void
    {
        $this->languageRepository->method('findOneByLocale')->willThrowException(new LanguageNotFoundException());

        $translation = (new TestTranslation())->setLocale('notexistinglocale');

        $language = $this->extension->getLanguage($translation);

        $this->assertNull($language);
    }

    public function testGetLanguageWithNotEnabledLocaleShouldFail(): void
    {
        $this->languageRepository->method('findOneByLocale')->willThrowException(new LanguageNotFoundException());

        $translation = (new TestTranslation())->setLocale('it');

        $language = $this->extension->getLanguage($translation);

        $this->assertNull($language);
    }
}
