<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Repository;

use Webmunkeez\I18nBundle\Exception\SiteNotFoundException;
use Webmunkeez\I18nBundle\Model\LocalizedSite;
use Webmunkeez\I18nBundle\Model\Site;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteRepository implements SiteRepositoryInterface
{
    /**
     * @var array<Site>
     */
    private array $sites = [];

    public function __construct(array $sitesData, LanguageRepositoryInterface $languageRepository)
    {
        foreach ($sitesData as $siteData) {
            if (null !== $siteData['locale']) {
                $this->sites[] = (new LocalizedSite())
                    ->setHost($siteData['host'])
                    ->setPath($siteData['path'])
                    ->setLocale($siteData['locale'])
                    ->setLanguage($languageRepository->findOneByLocale($siteData['locale']));
            } else {
                $this->sites[] = (new Site())
                    ->setHost($siteData['host'])
                    ->setPath($siteData['path']);
            }
        }
    }

    public function findAll(): array
    {
        return $this->sites;
    }

    public function countAll(): int
    {
        return count($this->sites);
    }

    public function findAllLocalized(): array
    {
        return array_values(array_filter($this->sites, fn (Site $site): bool => $site instanceof LocalizedSite));
    }

    public function findOneByUrl(string $host, string $uri): Site
    {
        /** @var Site $site */
        foreach ($this->sites as $site) {
            if (
                $site->getHost() === $host
                && 1 === preg_match('/'.$site->getPath().'/', $uri)
            ) {
                return $site;
            }
        }

        throw new SiteNotFoundException();
    }
}
