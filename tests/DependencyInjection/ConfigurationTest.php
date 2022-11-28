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

    public function testProcessWithFullConfigurationShouldSucceed(): void
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

    public function testProcessWithoutConfigurationShoulFail(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), []);
    }

    public function testProcessWithoutEnabledLocalesFail(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['enabled_locales']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongTypeEnabledLocalesShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['enabled_locales'] = 'en';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['enabled_locales'] = [];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotExistingLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['enabled_locales'] = ['notexistinglocale'];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutDefaultLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['default_locale']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongTypeDefaultLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['default_locale'] = ['en'];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotExistingDefaultLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['default_locale'] = 'notexistinglocale';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotEnabledDefaultLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['default_locale'] = 'it';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSitesShouldSucceed(): void
    {
        $config = self::CONFIG;
        unset($config['sites']);

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $this->assertSame(self::CONFIG['enabled_locales'], $config['enabled_locales']);
        $this->assertSame(self::CONFIG['default_locale'], $config['default_locale']);
        $this->assertEqualsCanonicalizing([], $config['sites']);
    }

    public function testProcessWithoutSiteIdShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['sites'][0]['id']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongSiteIdFormatShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['id'] = 'wrongformatid';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSiteHostShouldSucceed(): void
    {
        $config = self::CONFIG;
        unset($config['sites'][0]['host']);

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $this->assertSame(self::CONFIG['enabled_locales'], $config['enabled_locales']);
        $this->assertSame(self::CONFIG['default_locale'], $config['default_locale']);
        $this->assertSame(self::CONFIG['sites'][0]['id'], $config['sites'][0]['id']);
        $this->assertSame('localhost', $config['sites'][0]['host']);
        $this->assertSame(self::CONFIG['sites'][0]['path'], $config['sites'][0]['path']);
        $this->assertSame(self::CONFIG['sites'][0]['locale'], $config['sites'][0]['locale']);
    }

    public function testProcessWithoutSitePathShouldSucceed(): void
    {
        $config = self::CONFIG;
        unset($config['sites'][0]['path']);

        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $this->assertSame(self::CONFIG['enabled_locales'], $config['enabled_locales']);
        $this->assertSame(self::CONFIG['default_locale'], $config['default_locale']);
        $this->assertSame(self::CONFIG['sites'][0]['id'], $config['sites'][0]['id']);
        $this->assertSame(self::CONFIG['sites'][0]['host'], $config['sites'][0]['host']);
        $this->assertSame('^\/', $config['sites'][0]['path']);
        $this->assertSame(self::CONFIG['sites'][0]['locale'], $config['sites'][0]['locale']);
    }

    public function testProcessWithoutSiteLocaleShouldSucceed(): void
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

    public function testProcessWithNotExistingSiteLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['locale'] = 'notexistinglocale';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotEnabledSiteLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['sites'][0]['locale'] = 'it';

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }
}
