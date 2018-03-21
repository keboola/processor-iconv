<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\BaseComponent;
use Keboola\Component\UserException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Component extends BaseComponent
{
    public function run(): void
    {
        /** @var Config $config */
        $config = $this->getConfig();
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
                $destinationDir = $this->getDataDir() . "/out/$dir/" . $inFile->getRelativePath();
                $fs->mkdir($destinationDir);
                $destinationFile = $destinationDir . '/' . $inFile->getFilename();
                $this->processFile($inFile, $destinationFile, $filter);
            }
        }
    }

    private function processFile(SplFileInfo $inFile, string $destinationFileName, string $filter) : void
    {
        try {
            $source = fopen($inFile->getPathname(), 'r');
            $destination = fopen($destinationFileName, 'w');
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ErrorException $e) {
            throw new \Exception('Failed opening in/out ' . $inFile->getPathname() . ' ' . $e->getMessage());
        }
        try {
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
        } finally {
            fclose($source);
            fclose($destination);
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
