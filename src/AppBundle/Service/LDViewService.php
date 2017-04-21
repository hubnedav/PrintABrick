<?php

namespace AppBundle\Service;

use AppBundle\Exception\ConvertingFailedException;
use League\Flysystem\File;
use League\Flysystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

//TODO enable file overwrite
class LDViewService
{
    /**
     * @var string LDView binary file path
     */
    private $ldview;

    /**
     * @var Filesystem
     */
    private $mediaFilesystem;

    /**
     * @var Filesystem
     */
    private $ldrawLibraryContext;

    /**
     * LDViewService constructor.
     *
     * @param string     $ldview          Path to LDView OSMesa binary file
     * @param Filesystem $mediaFilesystem Filesystem for generated web assets
     */
    public function __construct($ldview, $mediaFilesystem)
    {
        $this->ldview = $ldview;
        $this->mediaFilesystem = $mediaFilesystem;
    }

    /**
     * @param string $ldrawLibraryContext
     */
    public function setLDrawLibraryContext($ldrawLibraryContext)
    {
        $this->ldrawLibraryContext = $ldrawLibraryContext;
    }

    /**
     * Convert LDraw model from .dat format to .stl by using LDView
     * stores created file to $stlStorage filesystem.
     *
     * @param $file
     *
     * @throws ConvertingFailedException
     *
     * @return File
     */
    public function datToStl($file, $rewrite = false)
    {
        if (!$this->mediaFilesystem->has('models')) {
            $this->mediaFilesystem->createDir('models');
        }

        $newFile = 'models'.DIRECTORY_SEPARATOR.basename($file, '.dat').'.stl';

        if (!$this->mediaFilesystem->has($newFile) || $rewrite) {
            $this->runLDView([
                $file,
                '-LDrawDir='.$this->ldrawLibraryContext->getAdapter()->getPathPrefix(),
                '-ExportFiles=1',
                '-ExportSuffix=.stl',
                '-ExportsDir='.$this->mediaFilesystem->getAdapter()->getPathPrefix().'models',
            ]);

            // Check if file created successfully
            if ($this->mediaFilesystem->has($newFile)) {
                return $this->mediaFilesystem->get($newFile);
            }
        } else {
            return $this->mediaFilesystem->get($newFile);
        }

        throw new ConvertingFailedException($file, 'STL');
    }

    /**
     * Convert LDraw model from .dat format to .stl by using LDView
     * stores created file to $stlStorage filesystem.
     *
     * @param $file
     *
     * @throws ConvertingFailedException
     *
     * @return File
     */
    public function datToPng($file, $rewrite = false)
    {
        if (!$this->mediaFilesystem->has('images')) {
            $this->mediaFilesystem->createDir('images');
        }

        $newFile = 'images'.DIRECTORY_SEPARATOR.basename($file, '.dat').'.png';

        if (!$this->mediaFilesystem->has($newFile) || $this->rewrite) {
            $this->runLDView([
                $file,
                '-LDrawDir='.$this->ldrawLibraryContext->getAdapter()->getPathPrefix(),
                '-AutoCrop=0',
                '-SaveAlpha=0',
                '-BackgroundColor3=0xFFFFFF',
                '-DefaultColor3=0x136FC3',
                '-SnapshotSuffix=.png',
                '-HiResPrimitives=1',
                '-UseQualityStuds=1',
                '-UseQualityLighting=1',
                '-SaveHeight=600',
                '-SaveWidth=800',
                '-CurveQuality=12',
                '-DefaultLatLong=45,40',
                '-SaveDir='.$this->mediaFilesystem->getAdapter()->getPathPrefix().'images',
                '-SaveSnapshots=1',
            ]);

            // Check if file created successfully
            if ($this->mediaFilesystem->has($newFile)) {
                return $this->mediaFilesystem->get($newFile);
            }
        } else {
            return $this->mediaFilesystem->get($newFile);
        }

        throw new ConvertingFailedException($file, 'PNG');
    }

    /**
     * Call LDView process with $arguments.
     *
     * @param array $arguments
     */
    private function runLDView(array $arguments)
    {
        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($this->ldview)
            ->setArguments($arguments)
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process); //TODO
        }
    }
}
