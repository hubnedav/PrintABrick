<?php

namespace AppBundle\Service;

use AppBundle\Entity\Model;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class LDrawLoader
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string LDView binary file path
     */
    private $ldview;

    /**
     * @var string LDraw library root path
     */
    private $ldraw;

    /**
     * @var string
     */
    private $dataPath;


    public function __construct($em, $ldview, $dataPath)
    {
        $this->em = $em;
        $this->ldview = $ldview;
        $this->dataPath = $dataPath;
    }

    // LDraw
    public function loadModels($LDrawLibrary)
    {
        $finder = new Finder();
        $files = $finder->files()->name('*.dat')->depth('== 0')->in(getcwd().DIRECTORY_SEPARATOR.$LDrawLibrary.'/parts');

        $this->ldraw = getcwd().DIRECTORY_SEPARATOR.$LDrawLibrary;

        foreach ($files as $file) {
            $this->loadModelHeader($file);
        }
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

            $builder = new ProcessBuilder();
            $process = $builder
                ->setPrefix($this->ldview)
                ->setArguments([
                    $fileInfo->getRealPath(),
                    '-ExportFiles=1',
                    '-LDrawDir='.$this->ldraw,
                    '-ExportSuffix=.stl',
                    '-ExportsDir='.$this->dataPath,
                ])
                ->getProcess();


            $process->run();


            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            } else {
                var_dump($process->getOutput());
            }

//            $this->em->persist($model);
//            $this->em->flush();
        } else {
            throw new LogicException('loadHeader error'); //TODO
        }
        fclose($handle);
    }
}
