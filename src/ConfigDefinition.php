<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\Config\BaseConfigDefinition;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ConfigDefinition extends BaseConfigDefinition
{
    protected function getParametersDefinition(): ArrayNodeDefinition
    {
        $parametersNode = parent::getParametersDefinition();
        // @formatter:off
        /** @noinspection NullPointerExceptionInspection */
        $parametersNode
            ->children()
                ->scalarNode('source_encoding')->isRequired()->end()
                ->scalarNode('target_encoding')->defaultValue('UTF-8')->end()
                ->scalarNode('modifier')
                    ->defaultValue("")
                    ->validate()
                        ->ifNotInArray(['//TRANSLIT', '//IGNORE', ''])
                        ->thenInvalid('Modifier must be on of "//TRANSLIT" or "//IGNORE" or empty.')
                    ->end()
                ->end()
            ->end()
        ;
        // @formatter:on
        return $parametersNode;
    }
}
