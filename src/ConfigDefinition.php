<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Process\Process;

class ConfigDefinition extends BaseConfigDefinition
{
    private function isValidEncoding(string $encoding) : bool
    {
        $process = new Process('iconv -l');
        $process->mustRun();
        $encodings = preg_split("#[\n ,]#", $process->getOutput());
        array_walk($encodings, function (&$val) : void {
            $val = trim($val, " \n\\/");
        });
        $encodings = array_filter($encodings);
        return in_array(strtoupper($encoding), $encodings);
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
