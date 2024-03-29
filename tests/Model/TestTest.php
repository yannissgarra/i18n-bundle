<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Model;

use PHPUnit\Framework\TestCase;
use Webmunkeez\I18nBundle\Exception\TranslationNotFoundException;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\Test;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\TestTranslation;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestTest extends TestCase
{
    private Test $test;

    protected function setUp(): void
    {
        $this->test = (new Test())
            ->addTranslation((new TestTranslation())->setLocale('en'))
            ->addTranslation((new TestTranslation())->setLocale('fr'));
    }

    public function testGetTranslationsShouldSucceed(): void
    {
        $this->assertCount(2, $this->test->getTranslations());
        $this->assertSame('en', $this->test->getTranslations()[0]->getLocale());
        $this->assertSame('fr', $this->test->getTranslations()[1]->getLocale());
    }

    public function testGetTranslationShouldSucceed(): void
    {
        $this->assertInstanceOf(TestTranslation::class, $this->test->getTranslation('en'));
        $this->assertSame('en', $this->test->getTranslation('en')->getLocale());
    }

    public function testGetTranslationShouldThrowException(): void
    {
        $this->expectException(TranslationNotFoundException::class);

        $this->test->getTranslation('es');
    }
}
