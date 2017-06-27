<?php

namespace LoaderBundle\Service\Stl;

use LoaderBundle\Exception\ConvertingFailedException;
use LoaderBundle\Exception\FileNotFoundException;
use LoaderBundle\Exception\RenderFailedException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class StlRendererService
{
    /**
     * @var string Full path to povray binary
     */
    private $povray;

    /**
     * @var string Full path to tmp dir where to save .pov files during process of rendering stl
     */
    private $tmpDir;

    /**
     * @var string Full path to stl2pov binary
     */
    private $stl2pov;

    /**
     * @var string Full path to scene layout file
     */
    private $layout;

    /**
     * @var int Desired square image dimensions in pixels
     */
    private $size;

    /**
     * StlRendererService constructor.
     *
     * @param string $layout
     * @param string $povray
     * @param string $stl2pov
     * @param string $tmpDir
     */
    public function __construct($layout, $povray, $stl2pov, $tmpDir = null)
    {
        $this->stl2pov = $stl2pov;
        $this->povray = $povray;
        $this->layout = $layout;
        $this->size = 900;
        if ($tmpDir) {
            $this->tmpDir = $tmpDir;
        } else {
            $this->tmpDir = sys_get_temp_dir();
        }
    }

    /**
     * @param $file
     * @param $destinationDir
     * @param bool $cleanup
     *
     * @throws \Exception
     *
     * @return string
     */
    public function render($file, $destinationDir, $cleanup = true)
    {
        $povFile = $this->convertStlPov($file);

        try {
            $image = $this->renderPov($povFile, $destinationDir);
            if ($cleanup) {
                unlink($povFile);
            }

            return $image;
        } catch (\Exception $exception) {
            unlink($povFile);
            throw $exception;
        }
    }

    /**
     * Converts STL file to pov scene by calling stl2pov command line application.
     *
     * Generated file is saved to tmp directory specifed in constructor of this class and path to file is returned
     *
     * stl2pov (version 3.3.0) - https://github.com/rsmith-nl/stltools/releases/tag/3.3
     *
     * @param string $file The full path to stl file
     *
     * @throws ConvertingFailedException throws exception if there are problems converting stl file to pov
     * @throws FileNotFoundException     throws exception if source file not found
     *
     * @return string Return the full path to the generated pov scene
     */
    private function convertStlPov($file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        // Save the current working directory and change directory to tmp dir
        // stl2pov outputs converted file to current directory and destination can not be changed
        $cwd = getcwd();
        chdir($this->tmpDir);

        $filename = pathinfo($file)['filename'];

        // Run stl2pov conversion
        $processBuilder = new ProcessBuilder();
        $process = $processBuilder->setPrefix($this->stl2pov)
            ->setArguments([
                $file,
            ])
            ->getProcess();
        $process->mustRun();

        // Check if file created successfully
        if (!file_exists($filename.'.inc')) {
            throw new ConvertingFailedException($file, 'POV');
        }

        // Load contents of .inc file to variable
        $incFile = file_get_contents($filename.'.inc', LOCK_EX);
        // Replace mesh name in loaded inc file to match declaration in scene layout
        $incFile = preg_replace('/# declare m_(.*) = mesh/', '#declare m_MYSOLID = mesh', $incFile);

        // Remove no longer needed inc file
        unlink($filename.'.inc');
        chdir($cwd);

        // Get desired filepath of new pov file
        $outputFile = $this->tmpDir.DIRECTORY_SEPARATOR.$filename.'.pov';

        // Load contents of pov-ray layout file
        $layout = file_get_contents($this->layout);

        // Try to write contents of converted inc file and concat int with scene definitions
        if (!file_put_contents($outputFile, $incFile, LOCK_EX)) {
            throw new ConvertingFailedException($file, 'POV');
        }
        if (!file_put_contents($outputFile, $layout, FILE_APPEND | LOCK_EX)) {
            throw new ConvertingFailedException($file, 'POV');
        }
        if (!file_exists($outputFile)) {
            throw new ConvertingFailedException($file, 'POV');
        }

        unset($incFile);

        return $outputFile;
    }

    /**
     * Renders POV-Ray .pov file by calling povray command line application.
     *
     * http://www.povray.org/
     *
     * @param string $file The full path to .pov file to be rendered
     * @param string $to   Destination directory path
     *
     * @throws RenderFailedException throws exception if there are problems rendering image
     * @throws FileNotFoundException throws exception if source file not found
     *
     * @return string Full path to rendered image file
     */
    private function renderPov($file, $to)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        if (!file_exists($to)) {
            mkdir($to, 0777, true);
        }

        $filename = pathinfo($file)['filename'];

        $outputFile = "{$to}{$filename}.png";

        $processBuilder = new ProcessBuilder();

        //+I	- input file name
        //+FN	- PNG file format
        //+Wn	- Sets screen width to n pixels
        //+Hn	- Sets screen height to n pixels
        //+O	- output file
        //+Qn	- Set quality value to n (0 <= n <= 11)
        //+AMn	- use non-adaptive (n=1) or adaptive (n=2) supersampling
        //+A0.n	- perform antialiasing (if color change is above n percent)
        //-D	- Turns graphic display off
        $process = $processBuilder
            ->setPrefix($this->povray)
            ->setArguments([
                "+I\"{$file}\"",
                '+FN',
                "+W{$this->size}",
                "+H{$this->size}",
                "+O\"$outputFile\"",
                '+Q8',
                '+AM2',
                '+A0.5',
                '-D',
            ])->getProcess();

        $process->mustRun();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!file_exists($outputFile)) {
            throw new RenderFailedException("{$to}{$filename}.png");
        }

        return $outputFile;
    }
}
