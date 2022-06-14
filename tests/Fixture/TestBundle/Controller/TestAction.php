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
use Webmunkeez\I18nBundle\Test\Fixture\TestBundle\Model\TestTranslation;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class TestAction
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(string $locale): Response
    {
        return new Response($this->twig->render('test.html.twig', [
            'test_translation' => (new TestTranslation())->setLocale($locale),
        ]));
    }
}
