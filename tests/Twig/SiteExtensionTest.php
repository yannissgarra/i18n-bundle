<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Twig;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webmunkeez\I18nBundle\Model\Site;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;
use Webmunkeez\I18nBundle\Twig\SiteExtension;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteExtensionTest extends TestCase
{
    /**
     * @var SiteRepositoryInterface&MockObject
     **/
    private SiteRepositoryInterface $siteRepository;

    private SiteExtension $extension;

    protected function setUp(): void
    {
        /** @var SiteRepositoryInterface&MockObject $siteRepository */
        $siteRepository = $this->getMockBuilder(SiteRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->siteRepository = $siteRepository;

        $this->extension = new SiteExtension($this->siteRepository);
    }

    public function testGetSitesShouldSucceed(): void
    {
        $sites = [
            (new Site())->setHost('example.com')->setPath('/fr'),
            (new Site())->setHost('example.com')->setPath('/en'),
        ];

        $this->siteRepository->method('findAll')->willReturn($sites);

        $this->assertSame($sites, $this->extension->getSites());
    }
}
