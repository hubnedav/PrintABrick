<?php

namespace App\Service\Stl;

use App\Service\Stl\Exception\ConversionFailedException;
use App\Service\Stl\Exception\LDLibraryMissingException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class StlConverter
{
    /**
     * @var string LDView binary file path
     */
    private $ldview;

    private StlFixer $stlFixer;
    private FilesystemInterface $mediaFilesystem;
    private FilesystemInterface $ldrawFilesystem;
    private string $iniFile;

    /**
     * StlConverterService constructor.
     *
     * @param string              $ldview          Path to LDView OSMesa binary file
     * @param FilesystemInterface $mediaFilesystem Filesystem for generated web assets
     */
    public function __construct(string $ldview, string $iniFile, FilesystemInterface $mediaFilesystem, FilesystemInterface $ldrawFilesystem, StlFixer $stlFixer)
    {
        $this->ldview = $ldview;
        $this->iniFile = $iniFile;
        $this->mediaFilesystem = $mediaFilesystem;
        $this->ldrawFilesystem = $ldrawFilesystem;
        $this->stlFixer = $stlFixer;
    }

    public function setLDrawFilesystem(FilesystemInterface $ldrawFilesystem)
    {
        $this->ldrawFilesystem = $ldrawFilesystem;
    }

    public function datToStl(string $path, ?\DateTime $lastChange = null): string
    {
        if (!$this->ldrawFilesystem->has('parts')) {
            throw new LDLibraryMissingException();
        }

        if (!$this->mediaFilesystem->has('models')) {
            $this->mediaFilesystem->createDir('models');
        }

        $newFilePath = 'models'.DIRECTORY_SEPARATOR.basename($path, '.dat').'.stl';

        if (!$this->mediaFilesystem->has($newFilePath) ||
            ($lastChange && ($this->mediaFilesystem->getTimestamp($newFilePath) < $lastChange->getTimestamp()))
        ) {
            $this->runLDView(
                [
                    $path,
                    '-LDrawDir='.$this->ldrawFilesystem->getAdapter()->getPathPrefix(),
                    '-ExportFiles=1',
                    '-ExportSuffix=.stl',
                    '-UseQualityStuds=1',
                    '-iniFile='.$this->iniFile,
                    '-ExportsDir='.$this->mediaFilesystem->getAdapter()->getPathPrefix().'models',
                ]
            );

            // Check if file created successfully
            if ($this->mediaFilesystem->has($newFilePath)) {
                $this->stlFixer->fix($this->mediaFilesystem->getAdapter()->getPathPrefix().$newFilePath);
            } else {
                throw new ConversionFailedException($path, $newFilePath, new FileNotFoundException($newFilePath));
            }
        }

        return $newFilePath;
    }

    /**
     * Call LDView process with $arguments.
     */
    private function runLDView(array $arguments)
    {
        $process = new Process(array_merge([$this->ldview], $arguments));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        if ($process->getOutput()) {
            throw new RuntimeException($process->getOutput());
        }
    }
}
