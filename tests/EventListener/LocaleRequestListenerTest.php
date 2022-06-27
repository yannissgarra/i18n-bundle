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
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmunkeez\I18nBundle\EventListener\LocaleRequestListener;
use Webmunkeez\I18nBundle\Model\Language;
use Webmunkeez\I18nBundle\Repository\LanguageRepositoryInterface;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class LocaleRequestListenerTest extends TestCase
{
    /** @var KernelInterface&MockObject */
    private KernelInterface $kernel;

    /**
     * @var LanguageRepositoryInterface&MockObject
     **/
    private LanguageRepositoryInterface $languageRepository;

    protected function setUp(): void
    {
        /** @var KernelInterface&MockObject $kernel */
        $kernel = $this->getMockForAbstractClass(Kernel::class, ['test', true]);
        $this->kernel = $kernel;

        /** @var LanguageRepositoryInterface&MockObject $languageRepository */
        $languageRepository = $this->getMockBuilder(LanguageRepositoryInterface::class)->disableOriginalConstructor()->getMock();
        $this->languageRepository = $languageRepository;
    }

    public function testWithEnabledLocaleShouldSucceed(): void
    {
        $this->languageRepository->expects($this->once())->method('localeExists')->willReturn(true);
        $this->languageRepository->expects($this->once())->method('findOneByLocale')->willReturn((new Language())->setLocale('en')->setName('English'));

        $listener = new LocaleRequestListener($this->languageRepository);

        $request = new Request(['_locale' => 'en']);

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertSame('en', $event->getRequest()->getLocale());
        $this->assertSame('en', $event->getRequest()->get('current-language')->getLocale());
        $this->assertSame('English', $event->getRequest()->get('current-language')->getName());
    }

    public function testWithAlreadySetCurrentLanguageShouldFail(): void
    {
        $this->languageRepository->expects($this->never())->method('localeExists');
        $this->languageRepository->expects($this->never())->method('findOneByLocale');
        $this->languageRepository->expects($this->never())->method('findOneDefault');

        $listener = new LocaleRequestListener($this->languageRepository);

        $request = new Request([], [], ['current-language' => (new Language())->setLocale('en')->setName('English')]);

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertSame('en', $event->getRequest()->getLocale());
        $this->assertSame('en', $event->getRequest()->get('current-language')->getLocale());
        $this->assertSame('English', $event->getRequest()->get('current-language')->getName());
    }

    public function testWithoutLocaleShouldFail(): void
    {
        $this->languageRepository->expects($this->never())->method('localeExists');
        $this->languageRepository->expects($this->never())->method('findOneByLocale');
        $this->languageRepository->expects($this->once())->method('findOneDefault')->willReturn((new Language())->setLocale('en')->setName('English'));

        $listener = new LocaleRequestListener($this->languageRepository);

        $request = new Request();

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertSame('en', $event->getRequest()->getLocale());
        $this->assertSame('en', $event->getRequest()->get('current-language')->getLocale());
        $this->assertSame('English', $event->getRequest()->get('current-language')->getName());
    }

    public function testWithNotExistingLocaleShouldFail(): void
    {
        $this->languageRepository->expects($this->once())->method('localeExists')->willReturn(false);
        $this->languageRepository->expects($this->never())->method('findOneByLocale');
        $this->languageRepository->expects($this->once())->method('findOneDefault')->willReturn((new Language())->setLocale('en')->setName('English'));

        $listener = new LocaleRequestListener($this->languageRepository);

        $request = new Request(['_locale' => 'notexistinglocale']);

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertSame('en', $event->getRequest()->getLocale());
        $this->assertSame('en', $event->getRequest()->get('current-language')->getLocale());
        $this->assertSame('English', $event->getRequest()->get('current-language')->getName());
    }

    public function testWithNotEnabledLocaleShouldFail(): void
    {
        $this->languageRepository->expects($this->once())->method('localeExists')->willReturn(false);
        $this->languageRepository->expects($this->never())->method('findOneByLocale');
        $this->languageRepository->expects($this->once())->method('findOneDefault')->willReturn((new Language())->setLocale('en')->setName('English'));

        $listener = new LocaleRequestListener($this->languageRepository);

        $request = new Request(['_locale' => 'it']);

        $event = new RequestEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertSame('en', $event->getRequest()->getLocale());
        $this->assertSame('en', $event->getRequest()->get('current-language')->getLocale());
        $this->assertSame('English', $event->getRequest()->get('current-language')->getName());
    }
}
