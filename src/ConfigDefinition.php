<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    private function isValidEncoding(string $encoding) : bool
    {
        try {
            iconv($encoding, 'UTF-8', 'abc');
            return true;
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {
            return false;
        }
    }

    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
                ->scalarNode('source_encoding')
                    ->isRequired()
                    ->validate()
                        ->ifTrue(function ($encoding) {
                            return !$this->isValidEncoding($encoding);
                        })
                        ->thenInvalid('Source encoding is not valid')
                    ->end()
                ->end()
                ->scalarNode('target_encoding')
                    ->defaultValue('UTF-8')
                    ->validate()
                    ->ifTrue(function ($encoding) {
                        return !$this->isValidEncoding($encoding);
                    })
                    ->thenInvalid('Target encoding is not valid')
                    ->end()
                ->end()
                ->scalarNode('on_error')
                    ->defaultValue("fail")
                    ->validate()
                        ->ifNotInArray(['transliterate', 'ignore', 'fail'])
                        ->thenInvalid('Modifier must be on of "transliterate" or "ignore" or "fail".')
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
