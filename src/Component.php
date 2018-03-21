<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\BaseComponent;
use Keboola\Component\UserException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Component extends BaseComponent
{
    public function run(): void
    {
        /** @var Config $config */
        $config = $this->getConfig();
        try {
            iconv($config->getSourceEncoding(), 'UTF-8', 'abc');
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {
            throw new UserException('Source encoding ' . $config->getSourceEncoding() . ' is invalid.');
        }
        try {
            iconv('UTF-8', $config->getTargetEncoding(), 'abc');
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {
            throw new UserException('Target encoding ' . $config->getTargetEncoding() . ' is invalid.');
        }
        $filter = 'convert.iconv.' . $config->getSourceEncoding() .  '/' .
            $config->getTargetEncoding() . $config->getModifier();

        $finder = new Finder();
        $finder->in($this->getDataDir() . '/in/tables')->files();
        $this->processDir($finder, 'tables', $filter);
        $finder = new Finder();
        $finder->in($this->getDataDir() . '/in/files')->files();
        $this->processDir($finder, 'files', $filter);
    }

    private function processDir(Finder $finder, string $dir, string $filter) : void
    {
        $fs = new Filesystem();
        foreach ($finder as $inFile) {
            if ($inFile->getExtension() == 'manifest') {
                // copy manifest without modification
                $fs->copy($inFile->getPathname(), $this->getDataDir() . "/out/$dir/" . $inFile->getFilename());
            } else {
                try {
                    $source = fopen($inFile->getPathname(), 'r');
                    $destinationDir = $this->getDataDir() . "/out/$dir/" . $inFile->getRelativePath();
                    $fs->mkdir($destinationDir);
                    $destination = fopen(
                        $destinationDir . '/' . $inFile->getFilename(),
                        'w'
                    );
                } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {
                    throw new \Exception('Failed opening in/out ' . $dir . ' ' . $e->getMessage());
                }

                stream_filter_append($destination, $filter, STREAM_FILTER_WRITE);
                $converted = '';
                try {
                    while (!feof($source)) {
                        $converted = fread($source, 1000);
                        fwrite($destination, $converted);
                    }
                } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {
                    throw new UserException('Conversion failed in ' . $inFile->getFilename() . '. ' .
                        $e->getMessage() . '. Failing text: ' . $converted);
                }
                fclose($source);
                fclose($destination);
            }
        }
    }

    protected function getConfigClass(): string
    {
        return Config::class;
    }

    protected function getConfigDefinitionClass(): string
    {
        return ConfigDefinition::class;
    }
}
