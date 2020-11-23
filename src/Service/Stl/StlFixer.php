<?php

namespace App\Service\Stl;

use App\Exception\FileNotFoundException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StlFixer
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
        $output = $output ?: $input;

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
     */
    private function runADMesh(array $arguments)
    {
        $process = new Process(array_merge([$this->ADMesh], $arguments));
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
