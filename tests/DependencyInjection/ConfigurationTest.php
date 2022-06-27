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
        'sites' => [
            [
                'id' => '831ee06a-63b5-41ee-8506-4b75dea2f7cf',
                'host' => 'example.com',
                'path' => '^\/fr',
                'locale' => 'en',
            ],
        ],
    ];

    public function testProcessWithFullConfigurationShouldSucceed()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => self::CONFIG]);

        $this->assertSame(self::CONFIG['enabled_locales'], $config['enabled_locales']);
        $this->assertSame(self::CONFIG['default_locale'], $config['default_locale']);
        $this->assertSame(self::CONFIG['sites'][0]['id'], $config['sites'][0]['id']);
        $this->assertSame(self::CONFIG['sites'][0]['host'], $config['sites'][0]['host']);
        $this->assertSame(self::CONFIG['sites'][0]['path'], $config['sites'][0]['path']);
        $this->assertSame(self::CONFIG['sites'][0]['locale'], $config['sites'][0]['locale']);
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

    public function testProcessWithoutSitesShouldSucceed()
    {
        $config = self::CONFIG;
        unset($config['sites']);

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $this->assertSame(self::CONFIG['enabled_locales'], $config['enabled_locales']);
        $this->assertSame(self::CONFIG['default_locale'], $config['default_locale']);
        $this->assertSame([], $config['sites']);
    }

    public function testProcessWithoutSiteIdShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['sites'][0]['id']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongSiteIdFormatShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['id'] = 'wrongformatid';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSiteHostShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['sites'][0]['host']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongFormatSiteHostShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['host'] = 'wrongformaturl';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithAnotherWrongFormatSiteHostShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['host'] = '_example.com';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSitePathShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['sites'][0]['path']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSiteLocaleShouldSucceed()
    {
        $config = self::CONFIG;
        unset($config['sites'][0]['locale']);

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $this->assertSame(self::CONFIG['enabled_locales'], $config['enabled_locales']);
        $this->assertSame(self::CONFIG['default_locale'], $config['default_locale']);
        $this->assertSame(self::CONFIG['sites'][0]['id'], $config['sites'][0]['id']);
        $this->assertSame(self::CONFIG['sites'][0]['host'], $config['sites'][0]['host']);
        $this->assertSame(self::CONFIG['sites'][0]['path'], $config['sites'][0]['path']);
        $this->assertNull($config['sites'][0]['locale']);
    }

    public function testProcessWithNotExistingSiteLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['locale'] = 'notexistinglocale';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotEnabledSiteLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['locale'] = 'es';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }
}
