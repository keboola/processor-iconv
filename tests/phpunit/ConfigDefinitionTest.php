<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv\Tests;

use Keboola\Component\BaseComponent;
use Keboola\ProcessorIconv\ConfigDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigDefinitionTest extends TestCase
{
    public function testPass() : void
    {
        $sourceConfig = [
            'parameters' => [
                'source_encoding' => 'UTF-8',
            ],
        ];
        $targetConfig = [
            'parameters' => [
                'source_encoding' => 'UTF-8',
                'target_encoding' => 'UTF-8',
                'on_error' => 'fail',
            ],
        ];
        $processor = new Processor();
        $cfgDefinition = new ConfigDefinition();
        $processedConfig = $processor->processConfiguration($cfgDefinition, [$sourceConfig]);
        self::assertEquals($targetConfig, $processedConfig);
    }


    /**
     * @dataProvider invalidConfigurationProvider
     * @param array $configuration
     * @param string $message
     */
    public function testFail(array $configuration, string $message) : void
    {
        putenv('KBC_DATADIR=' . __DIR__ . '/../functional/basic/source/data');
        $c = new BaseComponent();
        $c->setEnvironment();

        $processor = new Processor();
        $cfgDefinition = new ConfigDefinition();
        self::expectException(InvalidConfigurationException::class);
        self::expectExceptionMessage($message);
        $processor->processConfiguration($cfgDefinition, [$configuration]);
    }

    public function invalidConfigurationProvider() : array
    {
        return [
            [
                [
                    'parameters' => [
                        'source_encoding' => 'WTF-8',
                    ],
                ],
                'Source encoding is not valid',
            ],
            [
                [
                    'parameters' => [
                        'target_encoding' => 'WTF-8',
                    ],
                ],
                'The child node "source_encoding" at path "root.parameters" must be configured.',
            ],
            [
                [
                    'parameters' => [
                        'source_encoding' => 'UTF-8',
                        'target_encoding' => 'WTF-8',
                    ],
                ],
                'Target encoding is not valid',
            ],
            [
                [
                    'parameters' => [
                        'source_encoding' => 'UTF-8',
                        'on_error' => 'cry',
                    ],
                ],
                'Modifier must be on of "transliterate" or "ignore" or "fail".',
            ],
        ];
    }
}
