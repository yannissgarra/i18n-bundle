<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Serializer\Normalizer;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\TestTranslation;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LanguageAwareNormalizerFunctionalTest extends KernelTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = static::getContainer()->get('serializer');
    }

    public function testNormalizeWithAlreadySetLanguageShouldSucceed(): void
    {
        $translation = (new TestTranslation())
            ->setLocale('fr') // force test that language repository is not called
            ->setLanguage((new Language())->setLocale('en')->setName('English'));

        $json = $this->serializer->serialize($translation, JsonEncoder::FORMAT);

        $this->assertSame('{"locale":"fr","language":{"locale":"en","name":"English"}}', $json);
    }

    public function testNormalizeWithExistingLocaleShouldSucceed(): void
    {
        $translation = (new TestTranslation())->setLocale('en');

        $json = $this->serializer->serialize($translation, JsonEncoder::FORMAT);

        $this->assertSame('{"locale":"en","language":{"locale":"en","name":"English"}}', $json);
    }

    public function testNormalizeWithNotExistingLocaleShouldFail(): void
    {
        $translation = (new TestTranslation())->setLocale('es');

        $json = $this->serializer->serialize($translation, JsonEncoder::FORMAT);

        $this->assertSame('{"locale":"es","language":null}', $json);
    }
}
