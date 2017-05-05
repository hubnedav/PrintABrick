<?php

namespace AppBundle\Service\Stl;

use AppBundle\Exception\FileNotFoundException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class StlFixerService
{
    private $ADMesh;

    /**
     * StlFixerService constructor.
     *
     * @param $ADMesh
     */
    public function __construct($ADMesh)
    {
        $this->ADMesh = $ADMesh;
    }

    /**
     * Rotate, scale stl file and save in binary format.
     *
     * @param $file
     *
     * @throws FileNotFoundException
     */
    public function fix($file)
    {
        if (file_exists($file)) {
            $this->runADMesh([
                $file,
                '--x-rotate=-90',
                '--scale=10',
                '--no-check',
                "--write-binary-stl={$file}",
            ]);
        } else {
            throw new FileNotFoundException($file);
        }
    }

    /**
     * Call ADMesh process with $arguments.
     *
     * @param array $arguments
     */
    private function runADMesh(array $arguments)
    {
        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($this->ADMesh)
            ->setArguments($arguments)
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
