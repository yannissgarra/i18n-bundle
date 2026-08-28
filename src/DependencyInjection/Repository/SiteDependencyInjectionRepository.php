<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\DependencyInjection\Repository;

use Webmunkeez\I18nBundle\Exception\SiteNotFoundException;
use Webmunkeez\I18nBundle\Model\LocalizedSite;
use Webmunkeez\I18nBundle\Model\Site;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteDependencyInjectionRepository implements SiteRepositoryInterface
{
    /**
     * @var array<Site>
     */
    private array $sites = [];

    /**
     * @var array<Site>
     */
    private array $sitesByPosition = [];

    public function __construct(array $sitesData, LanguageRepositoryInterface $languageRepository)
    {
        foreach (array_values($sitesData) as $siteData) {
            if (null !== $siteData['locale']) {
                $site = (new LocalizedSite())
                    ->setHost($siteData['host'])
                    ->setPath($siteData['path'])
                    ->setLocale($siteData['locale'])
                    ->setLanguage($languageRepository->findOneByLocale($siteData['locale']));
            } else {
                $site = (new Site())
                    ->setHost($siteData['host'])
                    ->setPath($siteData['path']);
            }

            $this->sites[] = $site;
            $this->sitesByPosition[$siteData['position']] = $site;
        }

        ksort($this->sitesByPosition);
        $this->sitesByPosition = array_values($this->sitesByPosition);
    }

    public function findAll(): array
    {
        return $this->sitesByPosition;
    }

    public function countAll(): int
    {
        return count($this->sites);
    }

    public function findAllLocalized(): array
    {
        return array_values(array_filter($this->sitesByPosition, fn (Site $site): bool => $site instanceof LocalizedSite));
    }

    public function findOneByUrl(string $host, string $uri): Site
    {
        /** @var Site $site */
        foreach ($this->sites as $site) {
            if (
                (
                    null === $site->getHost() 
                    || $site->getHost() === $host
                ) && (
                    null === $site->getPath()
                    || 1 === preg_match('#^'.preg_quote($site->getPath(), '#').'(?:/|$)#', $uri)
                )
            ) {
                return $site;
            }
        }

        throw new SiteNotFoundException();
    }
}
