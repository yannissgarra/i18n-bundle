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
interface SiteRepositoryInterface
{
    /**
     * @return array<Site>
     */
    public function findAll(): array;

    public function countAll(): int;

    /**
     * @return array<LocalizedSite>
     */
    public function findAllLocalized(): array;

    /**
     * @throws SiteNotFoundException
     */
    public function findOneByUrl(string $host, string $uri): Site;
}
