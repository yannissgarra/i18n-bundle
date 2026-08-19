<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\PostTranslation;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class PostAction
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(string $locale, ?Language $language = null): Response
    {
        $translation = (new PostTranslation())->setLocale($locale);

        if (null !== $language) {
            $translation = (new PostTranslation())
                ->setLocale($locale)
                ->setLanguage($language);
        }

        return new Response($this->twig->render('post.html.twig', [
            'test_translation' => $translation,
            'locale' => $locale,
        ]));
    }
}
