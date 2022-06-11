<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18NBundle\Test\Fixture\TestBundle\Model;

use Webmunkeez\I18NBundle\Model\TranslationAwareInterface;
use Webmunkeez\I18NBundle\Model\TranslationInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class Test implements TranslationAwareInterface
{
    private array $translations;

    public function getTranslations(): iterable
    {
        return $this->translations;
    }

    public function addTranslation(TranslationInterface $translation): self
    {
        $this->translations[] = $translation;

        return $this;
    }
}
