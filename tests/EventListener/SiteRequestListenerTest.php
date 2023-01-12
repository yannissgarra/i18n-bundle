<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Uid\Uuid;
use Webmunkeez\I18nBundle\EventListener\SiteRequestListener;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Model\LocalizedSite;
use Webmunkeez\I18nBundle\Model\Site;
use Webmunkeez\I18nBundle\Repository\SiteRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class SiteRequestListenerTest extends TestCase
{
    /** @var KernelInterface&MockObject */
    private KernelInterface $kernel;

    /**
     * @var SiteRepositoryInterface&MockObject
     **/
    private SiteRepositoryInterface $siteRepository;

    private LocalizedSite $localizedSite;

    private Site $site;

    protected function setUp(): void
    {
        /** @var KernelInterface&MockObject $kernel */
        $kernel = $this->getMockForAbstractClass(Kernel::class, ['test', true]);
        $this->kernel = $kernel;

        /** @var SiteRepositoryInterface&MockObject $siteRepository */
        $siteRepository = $this->getMockBuilder(SiteRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->siteRepository = $siteRepository;

        $this->localizedSite = (new LocalizedSite())
            ->setId(Uuid::fromString('046842f7-f786-4d0b-9733-3369832081ab'))
            ->setHost('example.com')
            ->setPath('^\/')
            ->setLocale('en')
            ->setLanguage((new Language())->setLocale('en')->setName('English'));

        $this->site = (new Site())
            ->setId(Uuid::fromString('8863ca1e-9cb5-4d5e-b623-02e1b6dfa35d'))
            ->setHost('example.com')
            ->setPath('^\/api');
    }

    public function testWithLocalizedUrlShouldSucceed(): void
    {
        $this->siteRepository->expects($this->once())->method('countAll')->willReturn(4);
        $this->siteRepository->expects($this->once())->method('findOneByUrl')->willReturn($this->localizedSite);

        $listener = new SiteRequestListener($this->siteRepository);

        $request = Request::create('https://example.com/test');

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertInstanceOf(LocalizedSite::class, $event->getRequest()->get('current-site'));
        $this->assertTrue($event->getRequest()->get('current-site')->getId()->equals($this->localizedSite->getId()));
        $this->assertSame($this->localizedSite->getHost(), $event->getRequest()->get('current-site')->getHost());
        $this->assertSame($this->localizedSite->getPath(), $event->getRequest()->get('current-site')->getPath());
        $this->assertSame($this->localizedSite->getLocale(), $event->getRequest()->get('current-site')->getLocale());
        $this->assertSame($this->localizedSite->getLocale(), $event->getRequest()->getLocale());
        $this->assertSame($this->localizedSite->getLocale(), $event->getRequest()->get('current-language')->getLocale());
        $this->assertSame('English', $event->getRequest()->get('current-language')->getName());
    }

    public function testWithUnlocalizedUrlShouldSucceed(): void
    {
        $this->siteRepository->expects($this->once())->method('countAll')->willReturn(4);
        $this->siteRepository->expects($this->once())->method('findOneByUrl')->willReturn($this->site);

        $listener = new SiteRequestListener($this->siteRepository);

        $request = Request::create('https://example.com/api/test');

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertInstanceOf(Site::class, $event->getRequest()->get('current-site'));
        $this->assertTrue($event->getRequest()->get('current-site')->getId()->equals($this->site->getId()));
        $this->assertSame($this->site->getHost(), $event->getRequest()->get('current-site')->getHost());
        $this->assertSame($this->site->getPath(), $event->getRequest()->get('current-site')->getPath());
        $this->assertNull($event->getRequest()->get('current-language'));
    }

    public function testWithNotExistingUrlShouldThrowException(): void
    {
        $this->siteRepository->expects($this->once())->method('countAll')->willReturn(4);
        $this->siteRepository->expects($this->once())->method('findOneByUrl')->willThrowException(new NotFoundHttpException());

        $listener = new SiteRequestListener($this->siteRepository);

        $request = Request::create('https://_example.com/test');

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(NotFoundHttpException::class);

        $listener->onKernelRequest($event);
    }

    public function testWithoutSiteDefinedShouldFail(): void
    {
        $this->siteRepository->expects($this->once())->method('countAll')->willReturn(0);
        $this->siteRepository->expects($this->never())->method('findOneByUrl');

        $listener = new SiteRequestListener($this->siteRepository);

        $request = Request::create('https://example.com/test');

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertNull($event->getRequest()->get('current-site'));
    }
}
