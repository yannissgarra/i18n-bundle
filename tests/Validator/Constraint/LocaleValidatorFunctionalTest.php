<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Validator\Constraint;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\TestTranslation;
use Webmunkeez\I18nBundle\Validator\Constraint\Locale;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LocaleValidatorFunctionalTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = static::getContainer()->get('test_validator');
    }

    public function testValidateAttributeShouldSucceed()
    {
        $translation = (new TestTranslation())->setLocale('en');

        $violations = $this->validator->validate($translation);

        $this->assertCount(0, $violations);
    }

    public function testValidateAttributeWithNotExistingLocaleShouldFail()
    {
        $translation = (new TestTranslation())->setLocale('es');

        $violations = $this->validator->validate($translation);

        $this->assertCount(1, $violations);
        $this->assertSame((new Locale())->message, $violations[0]->getMessage());
    }
}
