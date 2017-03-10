<?php

namespace AppBundle\Service;

use League\Flysystem\File;
use League\Flysystem\Filesystem;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Process\ProcessBuilder;

class LDViewService
{
    /**
     * @var string LDView binary file path
     */
    private $ldview;

    /**
     * @var \League\Flysystem\Filesystem
     */
    private $stlStorage;

    /**
     * LDViewService constructor.
     *
     * @param string     $ldview Path to LDView OSMesa binary file
     * @param Filesystem $stlStorage Filesystem for generated stl model files
     */
    public function __construct($ldview, $stlStorage)
    {
        $this->ldview = $ldview;
        $this->stlStorage = $stlStorage;
    }

    /**
     * Convert LDraw model from .dat format to .stl by using LDView
     * stores created file to $stlStorage filesystem
     *
     * @param Filesystem $LDrawDir
     *
     * @return File
     */
    public function datToStl($file, $LDrawDir)
    {
        $stlFilename = $file['filename'].'.stl';

        if (!$this->stlStorage->has($stlFilename)) {
            $builder = new ProcessBuilder();
            $process = $builder
                ->setPrefix($this->ldview)
                ->setArguments([
                    $LDrawDir->getAdapter()->getPathPrefix().$file['path'],
                    '-LDrawDir='.$LDrawDir->getAdapter()->getPathPrefix(),
                    '-ExportFile='.$this->stlStorage->getAdapter()->getPathPrefix().$stlFilename,
                ])
                ->getProcess();

            $process->run();

            if (!$this->stlStorage->has($stlFilename)) {
                throw new LogicException($file['basename'].': new file not found'); //TODO
            } elseif (!$process->isSuccessful()) {
                throw new LogicException($file['basename'].' : '.$process->getOutput()); //TODO
            }
        }

        return $this->stlStorage->get($stlFilename);
    }
}
