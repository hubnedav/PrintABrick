<?php

namespace AppBundle\Loader;

use AppBundle\Entity\Model;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class LDrawLoader extends Loader
{
    /**
     * @var string LDView binary file path
     */
    private $ldview;

    /**
     * @var Filesystem
     */
    private $ldraw;

    /**
     * @var \League\Flysystem\Filesystem
     */
    private $dataPath;

    public function __construct($em, $ldview, $dataPath)
    {
        $this->em = $em;
        $this->ldview = $ldview;
        $this->dataPath = $dataPath;
    }

    public function loadModels($LDrawLibrary)
    {
        $adapter = new Local(getcwd().DIRECTORY_SEPARATOR.$LDrawLibrary);
        $this->ldraw = new Filesystem($adapter);
//        $files = $this->ldraw->get('parts')->getContents();

        $finder = new Finder();
        $files = $finder->files()->name('*.dat')->depth('== 0')->in(getcwd().DIRECTORY_SEPARATOR.$LDrawLibrary.DIRECTORY_SEPARATOR.'parts');

        $progressBar = new ProgressBar($this->output, $files->count());
        $progressBar->setFormat('very_verbose');
        $progressBar->setMessage('Loading LDraw library models');
        $progressBar->setFormat('%message:6s% %current%/%max% [%bar%]%percent:3s%% (%elapsed:6s%/%estimated:-6s%)');
        $progressBar->start();
        foreach ($files as $file) {
            $this->loadModelHeader($file);
            $this->createStlFile($file);
            $progressBar->advance();
        }
        $progressBar->finish();
    }

    private function loadModelHeader(SplFileInfo $fileInfo)
    {
        $handle = fopen($fileInfo->getRealPath(), 'r');
        if ($handle) {
            $firstLine = fgets($handle);
            $description = trim(substr($firstLine, 2));
            $model = new Model();
            $model->setFile($fileInfo->getFilename());
            $p['category'] = explode(' ', trim($description))[0];

            //TODO handle ~Moved to

            while (!feof($handle)) {
                $line = trim(fgets($handle));
                if ($line && ($line[0] == '1')) {
                    break;
                } elseif ($line && ($line[0] == '0' && strpos($line, '!CATEGORY '))) {
                    $p['category'] = trim(explode('!CATEGORY ', $line)[1]);
                } elseif ($line && ($line[0] == '0' && strpos($line, '!KEYWORDS '))) {
                    $keywords = explode(',', explode('!KEYWORDS ', $line)[1]);
                    foreach ($keywords as $k) {
                        $p['keywords'][] = trim($k);
                    }
                } elseif ($line && ($line[0] == '0' && strpos($line, 'Name: '))) {
                    $model->setNumber(trim(explode('.dat', explode('Name: ', $line)[1])[0]));
                } elseif ($line && ($line[0] == '0' && strpos($line, 'Author: '))) {
                    $model->setAuthor(explode('Author: ', $line)[1]);
                }
            }
//            $this->em->persist($model);
//            $this->em->flush();
        } else {
            throw new LogicException('loadHeader error'); //TODO
        }
        fclose($handle);
    }

    private function createStlFile(SplFileInfo $file)
    {
        $builder = new ProcessBuilder();
        $process = $builder
            ->setPrefix($this->ldview)
            ->setArguments([
//                $this->ldraw->getAdapter()->getPathPrefix().$file['path'],
                $file->getRealPath(),
                '-ExportFiles=1',
                '-LDrawDir='.$this->ldraw->getAdapter()->getPathPrefix(),
                '-ExportSuffix=.stl',
                '-ExportsDir='.$this->dataPath->getAdapter()->getPathPrefix(),
            ])
            ->getProcess();

        $process->run();

        if (!$process->isSuccessful() || !$this->dataPath->has(str_replace('.dat', '.stl', $file->getFilename()))) {
            throw new ProcessFailedException($process); //TODO
        }
    }
}
