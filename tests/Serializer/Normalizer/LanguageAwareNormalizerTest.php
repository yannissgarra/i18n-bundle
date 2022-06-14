<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Serializer\Normalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmunkeez\I18nBundle\Exception\LanguageNotFoundException;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Serializer\Normalizer\LanguageAwareNormalizer;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\TestTranslation;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageAwareNormalizerTest extends TestCase
{
    public const DATA = [
        'translation' => [
            'locale' => 'en',
            'language' => [
                'locale' => 'en',
                'name' => 'English',
            ],
        ],
    ];

    /**
     * @var NormalizerInterface&MockObject
     **/
    private NormalizerInterface $coreNormalizer;

    /**
     * @var LanguageRepositoryInterface&MockObject
     **/
    private LanguageRepositoryInterface $languageRepository;

    private LanguageAwareNormalizer $normalizer;

    protected function setUp(): void
    {
        /** @var NormalizerInterface&MockObject $coreNormalizer */
        $coreNormalizer = $this->getMockBuilder(NormalizerInterface::class)->disableOriginalConstructor()->getMock();
        $this->coreNormalizer = $coreNormalizer;

        /** @var LanguageRepositoryInterface&MockObject $languageRepository */
        $languageRepository = $this->getMockBuilder(LanguageRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->languageRepository = $languageRepository;

        $this->normalizer = new LanguageAwareNormalizer($this->languageRepository);
        $this->normalizer->setNormalizer($this->coreNormalizer);
    }

    public function testNormalizeWithExistingLocaleShouldSucceed(): void
    {
        $this->coreNormalizer->method('normalize')->willReturn(self::DATA['translation']);
        $this->languageRepository->method('findOneByLocale')->willReturn((new Language())->setLocale('en')->setName('English'));

        $translation = (new TestTranslation())->setLocale('en');

        $data = $this->normalizer->normalize($translation);

        $this->assertSame('en', $data['locale']);
        $this->assertSame('en', $data['language']['locale']);
        $this->assertSame('English', $data['language']['name']);
    }

    public function testNormalizeWithNotExistingLocaleShouldFail(): void
    {
        $this->coreNormalizer->method('normalize')->willReturn(['locale' => 'es', 'language' => null]);
        $this->languageRepository->method('findOneByLocale')->willThrowException(new LanguageNotFoundException());

        $translation = (new TestTranslation())->setLocale('es');

        $data = $this->normalizer->normalize($translation);

        $this->assertSame('es', $data['locale']);
        $this->assertNull($data['language']);
    }
}
