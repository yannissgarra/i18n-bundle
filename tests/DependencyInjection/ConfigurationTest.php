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
    public const DATA = [
        'enabled_locales' => ['en', 'fr'],
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
        $processedConfig = (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => self::DATA]);

        $this->assertEqualsCanonicalizing(self::DATA, $processedConfig);
    }

    public function testProcessWithoutConfigurationShoulFail(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(new Configuration(), []);
    }

    public function testProcessWithoutEnabledLocalesFail(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        unset($config['enabled_locales']);

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongTypeEnabledLocalesShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        $config['enabled_locales'] = 'en';

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        $config['enabled_locales'] = [];

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotExistingLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        $config['enabled_locales'] = ['notexistinglocale'];

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSitesShouldSucceed(): void
    {
        $config = self::DATA;
        unset($config['sites']);

        $processedConfig = (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $config['sites'] = [];

        $this->assertEqualsCanonicalizing($config, $processedConfig);
    }

    public function testProcessWithoutSiteIdShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        unset($config['sites'][0]['id']);

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithWrongSiteIdFormatShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        $config['sites'][0]['id'] = 'wrongformatid';

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutSiteHostShouldSucceed(): void
    {
        $config = self::DATA;
        unset($config['sites'][0]['host']);

        $processedConfig = (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $config['sites'][0]['host'] = 'localhost';

        $this->assertEqualsCanonicalizing($config, $processedConfig);
    }

    public function testProcessWithoutSitePathShouldSucceed(): void
    {
        $config = self::DATA;
        unset($config['sites'][0]['path']);

        $processedConfig = (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $config['sites'][0]['path'] = '^\/';

        $this->assertEqualsCanonicalizing($config, $processedConfig);
    }

    public function testProcessWithoutSiteLocaleShouldSucceed(): void
    {
        $config = self::DATA;
        unset($config['sites'][0]['locale']);

        $processedConfig = (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);

        $config['sites'][0]['locale'] = null;

        $this->assertEqualsCanonicalizing($config, $processedConfig);
    }

    public function testProcessWithNotExistingSiteLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        $config['sites'][0]['locale'] = 'notexistinglocale';

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotEnabledSiteLocaleShouldThrowException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::DATA;
        $config['sites'][0]['locale'] = 'it';

        (new Processor())->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }
}
