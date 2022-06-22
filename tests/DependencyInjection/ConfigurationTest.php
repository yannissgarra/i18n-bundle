<?php

/*
 * (c) Yannis Sgarra <hello@yannissgarra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Webmunkeez\I18nBundle\Test\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Webmunkeez\I18nBundle\DependencyInjection\Configuration;

/**
 * @author Yannis Sgarra <hello@yannissgarra.com>
 */
final class ConfigurationTest extends TestCase
{
    public const CONFIG = [
        'enabled_locales' => ['en', 'fr'],
        'default_locale' => 'en',
    ];

    public function testProcessWithFullConfigurationShouldSucceed()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => self::CONFIG]);

        $this->assertSame(self::CONFIG, $config);
    }

    public function testProcessWithoutConfigurationShoulFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), []);
    }

    public function testProcessWithoutEnabledLocalesFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['enabled_locales']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongTypeEnabledLocalesShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['enabled_locales'] = 'en';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['enabled_locales'] = [];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotExistingLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['enabled_locales'] = ['notexistinglocale'];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutDefaultLocaleFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['default_locale']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongTypeDefaultLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['default_locale'] = ['en'];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotExistingDefaultLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['default_locale'] = 'notexistinglocale';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotEnabledDefaultLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['default_locale'] = 'es';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }
}
