<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\Twig;

use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmunkeez\I18nBundle\Twig\DateTimeExtension;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class DateTimeExtensionTest extends TestCase
{
    public const DATA = [
        'date_interval.year_ago' => [
            '%count% year ago',
            '%count% years ago',
        ],
        'date_interval.month_ago' => [
            '%count% month ago',
            '%count% months ago',
        ],
        'date_interval.week_ago' => [
            '%count% week ago',
            '%count% weeks ago',
        ],
        'date_interval.day_ago' => [
            '%count% day ago',
            '%count% days ago',
        ],
        'date_interval.hour_ago' => [
            '%count% hour ago',
            '%count% hours ago',
        ],
        'date_interval.minute_ago' => [
            '%count% minute ago',
            '%count% minutes ago',
        ],
    ];

    private DateTimeExtension $extension;

    protected function setUp(): void
    {
        /** @var TranslatorInterface&MockObject $translator */
        $translator = $this->getMockBuilder(TranslatorInterface::class)->disableOriginalConstructor()->getMock();
        $translator->method('trans')->willReturnCallback(function () {
            $args = func_get_args();
            $translations = self::DATA[$args[0]];

            return $args[1]['%count%'] < 2 ? $translations[0] : $translations[1];
        });

        $this->extension = new DateTimeExtension($translator);
    }

    public function testGetAgoWithNowShouldSucceed(): void
    {
        $date = new DateTime();

        $this->assertSame(self::DATA['date_interval.minute_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithMinuteShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-1 minute');

        $this->assertSame(self::DATA['date_interval.minute_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithMinutesShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-5 minutes');

        $this->assertSame(self::DATA['date_interval.minute_ago'][1], $this->extension->getAgo($date));
    }

    public function testGetAgoWithHourShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-1 hour');

        $this->assertSame(self::DATA['date_interval.hour_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithHoursShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-5 hours');

        $this->assertSame(self::DATA['date_interval.hour_ago'][1], $this->extension->getAgo($date));
    }

    public function testGetAgoWithDayShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-1 day');

        $this->assertSame(self::DATA['date_interval.day_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithDaysShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-5 days');

        $this->assertSame(self::DATA['date_interval.day_ago'][1], $this->extension->getAgo($date));
    }

    public function testGetAgoWithWeekShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-1 week');

        $this->assertSame(self::DATA['date_interval.week_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithWeeksShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-3 weeks');

        $this->assertSame(self::DATA['date_interval.week_ago'][1], $this->extension->getAgo($date));
    }

    public function testGetAgoWithMonthShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-1 month');

        $this->assertSame(self::DATA['date_interval.month_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithMonthsShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-10 months');

        $this->assertSame(self::DATA['date_interval.month_ago'][1], $this->extension->getAgo($date));
    }

    public function testGetAgoWithYearShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-1 year');

        $this->assertSame(self::DATA['date_interval.year_ago'][0], $this->extension->getAgo($date));
    }

    public function testGetAgoWithYearsShouldSucceed(): void
    {
        $date = (new DateTime())->modify('-5 years');

        $this->assertSame(self::DATA['date_interval.year_ago'][1], $this->extension->getAgo($date));
    }
}
