<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmunkeez\I18nBundle\Model\Site;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteExtension extends AbstractExtension
{
    public function __construct(
        private readonly SiteRepositoryInterface $siteRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sites', [$this, 'getSites']),
        ];
    }

    /**
     * @return array<Site>
     */
    public function getSites(): array
    {
        return $this->siteRepository->findAll();
    }
}
