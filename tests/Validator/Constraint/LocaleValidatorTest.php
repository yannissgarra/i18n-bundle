<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Validator\Constraint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Validator\Constraint\Locale;
use Webmunkeez\I18nBundle\Validator\Constraint\LocaleValidator;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LocaleValidatorTest extends TestCase
{
    /**
     * @var ConstraintViolationBuilderInterface&MockObject
     */
    private ConstraintViolationBuilderInterface $constraintViolationBuilder;

    /**
     * @var ExecutionContextInterface&MockObject
     */
    private ExecutionContextInterface $executionContext;

    /**
     * @var LanguageRepositoryInterface&MockObject
     */
    private LanguageRepositoryInterface $languageRepository;

    protected function setUp(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->disableOriginalConstructor()->getMock();
        $this->constraintViolationBuilder = $constraintViolationBuilder;

        /** @var ExecutionContextInterface&MockObject $executionContext */
        $executionContext = $this->getMockBuilder(ExecutionContextInterface::class)->disableOriginalConstructor()->getMock();
        $this->executionContext = $executionContext;

        /** @var LanguageRepositoryInterface&MockObject $languageRepository */
        $languageRepository = $this->getMockBuilder(LanguageRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->languageRepository = $languageRepository;
    }

    public function testValidateShouldSucceed()
    {
        $this->languageRepository->method('localeExists')->willReturn(true);

        $validator = new LocaleValidator($this->languageRepository);

        $this->expectNotToPerformAssertions();

        $validator->validate('en', new Locale());
    }

    public function testValidateWithNotExistingLocaleShouldFail()
    {
        $this->languageRepository->method('localeExists')->willReturn(false);

        $validator = new LocaleValidator($this->languageRepository);
        $constraint = new Locale();

        $this->constraintViolationBuilder->expects($this->once())->method('addViolation')->willReturn(null);

        $this->executionContext->expects($this->once())->method('buildViolation')->with($constraint->message)->willReturn($this->constraintViolationBuilder);

        $validator->initialize($this->executionContext);

        $validator->validate('es', $constraint);
    }
}
