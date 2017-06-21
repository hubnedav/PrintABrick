<?php

namespace LoaderBundle\Service\Stl;

use LoaderBundle\Exception\FileNotFoundException;
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
     * @param $input
     * @param $output
     *
     * @throws FileNotFoundException
     */
    public function fix($input, $output = null)
    {
        $output = $output ? $output : $input;

        if (file_exists($input)) {
            $this->runADMesh([
                $input,
                '--x-rotate=-90',
                '--scale=10',
                '--no-check',
                "--write-binary-stl={$output}",
            ]);
        } else {
            throw new FileNotFoundException($input);
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
