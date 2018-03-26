<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\Config\BaseConfig;

class Config extends BaseConfig
{
    public function getSourceEncoding() : string
    {
        return $this->getValue(['parameters', 'source_encoding']);
    }

    public function getTargetEncoding() : string
    {
        return $this->getValue(['parameters', 'target_encoding']);
    }

    public function getModifier() : string
    {
        switch ($this->getValue(['parameters', 'on_error'])) {
            case 'transliterate':
                return '//TRANSLIT';
            case 'ignore':
                return '//IGNORE';
            case 'fail':
                return '';
        }
    }
}
