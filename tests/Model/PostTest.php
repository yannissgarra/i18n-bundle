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
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\Post;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\PostTranslation;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class PostTest extends TestCase
{
    private Post $post;

    protected function setUp(): void
    {
        $this->post = (new Post())
            ->addTranslation((new PostTranslation())->setLocale('en'))
            ->addTranslation((new PostTranslation())->setLocale('fr'));
    }

    public function testGetTranslationsShouldSucceed(): void
    {
        $this->assertCount(2, $this->post->getTranslations());
        $this->assertSame('en', $this->post->getTranslations()[0]->getLocale());
        $this->assertSame('fr', $this->post->getTranslations()[1]->getLocale());
    }

    public function testGetTranslationShouldSucceed(): void
    {
        $this->assertInstanceOf(PostTranslation::class, $this->post->getTranslation('en'));
        $this->assertSame('en', $this->post->getTranslation('en')->getLocale());
    }

    public function testGetTranslationShouldThrowException(): void
    {
        $this->expectException(TranslationNotFoundException::class);

        $this->post->getTranslation('es');
    }
}
