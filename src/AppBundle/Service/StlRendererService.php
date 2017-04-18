<?php

namespace AppBundle\Service;

use AppBundle\Exception\ConvertingFailedException;
use AppBundle\Exception\FileNotFoundException;
use AppBundle\Exception\RenderFailedException;
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
     * @param $layout
     * @param $povray
     * @param $stl2pov
     */
    public function __construct($layout, $povray, $stl2pov, $tmpDir = null)
    {
        $this->stl2pov = $stl2pov;
        $this->povray = $povray;
        $this->layout = $layout;
        $this->size = 900;
        if($tmpDir) {
            $this->tmpDir = $tmpDir;
        } else {
            $this->tmpDir = sys_get_temp_dir();
        }
    }


    /**
     * @param $file
     * @param $destinationDir
     * @param bool $cleanup
     * @return string
     * @throws \Exception
     */
    public function render($file, $destinationDir, $cleanup = true) {
        $povFile = $this->convertStlPov($file);

        try {
            $image = $this->renderPov($povFile, $destinationDir);
            if($cleanup) {
                unlink($povFile);
            }
            return $image;

        } catch (\Exception $exception) {
            unlink($povFile);
            throw $exception;
        }
    }


    /**
     * Converts STL file to pov scene by calling stl2pov command line application
     *
     * Generated file is saved to tmp directory specifed in constructor of this class and path to file is returned
     *
     *
     * stl2pov (version 2.5.0) - https://rsmith.home.xs4all.nl/software/py-stl-stl2pov.html
     *
     * @param string $file The full path to stl file
     * @return string Return the full path to the generated pov scene
     * @throws ConvertingFailedException throws exception if there are problems converting stl file to pov
     * @throws FileNotFoundException throws exception if source file not found
     */
    private function convertStlPov($file) {
        if(!file_exists($file)) {
            throw new FileNotFoundException($file);
        }

        $processBuilder = new ProcessBuilder();
        $process = $processBuilder->setPrefix($this->stl2pov)
            ->setArguments([
                $file
            ])
            ->getProcess();

        $process->start();

        $modelInc = null;

        // Read std output from stl2pov command
        foreach ($process as $type => $data) {
            if (Process::OUT === $type) {
                $modelInc .= $data;
            }
        };

        // Replace mesh name in loaded inc file to match declaration in scene layout
        $modelInc = preg_replace('/#declare m_(.*) = mesh/','#declare m_MYSOLID = mesh',$modelInc);

        // Get filename of new file
        $outputFile = $this->tmpDir.DIRECTORY_SEPARATOR.pathinfo($file)['filename'].'.pov';

        // Load contents of pov-ray layout file
        $layout = file_get_contents($this->layout);

        // Try to write contents of converted inc file and concat int with scene definition
        if (!file_put_contents($outputFile, $modelInc, LOCK_EX)) {
            throw new ConvertingFailedException($file, 'POV');
        }
        if(!file_put_contents($outputFile, $layout, FILE_APPEND | LOCK_EX)) {
            throw new ConvertingFailedException($file, 'POV');
        }
        if (!strlen(file_get_contents($outputFile))) {
            throw new ConvertingFailedException($file, 'POV');
        }

        return $outputFile;
    }


    /**
     * Renders POV-Ray .pov file by calling povray command line application
     *
     * http://www.povray.org/
     *
     * @param string $file The full path to .pov file to be rendered
     * @param $to
     * @return string Full path to rendered image file
     * @throws RenderFailedException throws exception if there are problems rendering image
     * @throws FileNotFoundException throws exception if source file not found
     */
    private function renderPov($file, $to) {
        if(!file_exists($file)) {
            throw new FileNotFoundException($file);
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
        $process = $processBuilder->setPrefix($this->povray)
            ->setArguments([
                "+I\"{$file}\"",
                "+FN",
                "+W{$this->size}",
                "+H{$this->size}",
                "+O\"$outputFile\"",
                "+Q8",
                "+AM2",
                "+A0.5",
                "-D",
            ])->getProcess();

        $process->run();

        if(!file_exists($outputFile)) {
            throw new RenderFailedException("{$to}{$filename}.png");
        }
        return $outputFile;
    }
}