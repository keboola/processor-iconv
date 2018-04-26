<?php

declare(strict_types=1);

namespace Keboola\ProcessorIconv;

use Keboola\Component\BaseComponent;
use Keboola\Component\UserException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Component extends BaseComponent
{
    public function run(): void
    {
        /** @var Config $config */
        $config = $this->getConfig();
        $finder = new Finder();
        $finder->in($this->getDataDir() . '/in/tables')->files();
        $this->processDir($finder, 'tables', $config);
        $finder = new Finder();
        $finder->in($this->getDataDir() . '/in/files')->files();
        $this->processDir($finder, 'files', $config);
    }

    private function processDir(Finder $finder, string $dir, Config $config) : void
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
                $this->processFile($inFile, $destinationFile, $config);
            }
        }
    }

    private function processFile(SplFileInfo $inFile, string $destinationFileName, Config $config) : void
    {
        $command = 'iconv';
        if ($config->ignoreErrors()) {
            $command .= ' -c';
        }
        $command .= ' --from-code=' . escapeshellarg($config->getSourceEncoding());
        $command .= ' --to-code=' . escapeshellarg($config->getTargetEncoding() . $config->getModifier()) ;
        $command .= ' ' . escapeshellarg($inFile->getPathname()) . ' > ' . escapeshellarg($destinationFileName);
        $process = new Process($command);
        try {
            $process->mustRun();
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ProcessFailedException $e) {
            throw new UserException('Conversion failed in ' . $inFile->getFilename() . '. ' .
                $e->getMessage() . '. Message: ' . $process->getErrorOutput());
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
