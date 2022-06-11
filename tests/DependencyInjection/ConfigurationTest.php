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
        'languages' => [
            ['locale' => 'en', 'name' => 'English'],
            ['locale' => 'fr', 'name' => 'Français'],
        ],
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

    public function testProcessWithoutLanguagesFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        unset($config['languages']);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutLanguageShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['languages'] = [];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['languages'] = [
            ['name' => 'English'],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithNotExistingLocaleShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['languages'] = [
            ['locale' => 'notexistinglocale', 'name' => 'English'],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }

    public function testProcessWithoutNameShouldFail()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = self::CONFIG;
        $config['languages'] = [
            ['locale' => 'en'],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(), ['webmunkeez_i18n' => $config]);
    }
}
