<?php

namespace AppBundle\Command\Loader;

use AppBundle\Entity\Category;
use AppBundle\Entity\Model;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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

    private $ldraw_url;

    public function __construct($em, $ldview, $dataPath, $ldraw_url)
    {
        /*
         * @var $em EntityManager
         * */
        $this->em = $em;
        $this->ldview = $ldview;
        $this->dataPath = $dataPath;
        $this->ldraw_url = $ldraw_url;
    }

    public function downloadLibrary()
    {
        $this->output->writeln('Downloading set_pieces.csv from Rebrickable.com');
        $temp = $this->downloadFile($this->ldraw_url);
        $temp_dir = tempnam(sys_get_temp_dir(), 'printabrick.');
        if (file_exists($temp_dir)) {
            unlink($temp_dir);
        }
        mkdir($temp_dir);
        $zip = new \ZipArchive();
        if ($zip->open($temp) != 'true') {
            echo 'Error :- Unable to open the Zip File';
        }
        $zip->extractTo($temp_dir);
        $zip->close();
        unlink($temp);

        return $temp_dir;
    }

    public function loadModels($LDrawLibrary)
    {
        //TODO Refactor, use flysystem
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
            $model = $this->loadPartHeader($file);
            $model->setFile($this->createStlFile($file)->getPath());

            $this->em->persist($model);
            $this->em->flush();

            $progressBar->advance();
        }
        $progressBar->finish();
    }

    /**
     * @param SplFileInfo $file
     *
     * @return Model
     */
    private function loadPartHeader($file)
    {
        $handle = fopen($file->getRealPath(), 'r');
        if ($handle) {
            $firstLine = false;

            $model = new Model();

            // read lines while line starts with 0 or is empty
            while (($line = trim(fgets($handle))) !== false && ($line ? $line[0] == '0' : true)) {
                if ($line !== '') {
                    $line = preg_replace('/^0 /', '', $line);

                    // 0 <CategoryName> <PartDescription>
                    if (!$firstLine) {
                        //TODO handle "~Moved to"
                        //TODO        "=" - alias name for other part kept for referece
                        //TODO        "_" shortcut

                        $array = explode(' ', trim($line), 2);
                        $category = isset($array[0]) ? $array[0] : '';
                        $model->setName($line);

                        $firstLine = true;
                    }
                    // 0 !CATEGORY <CategoryName>
                    elseif (strpos($line, '!CATEGORY ') === 0) {
                        $category = trim(preg_replace('/^!CATEGORY /', '', $line));
                    }
                    // 0 !KEYWORDS <first keyword>, <second keyword>, ..., <last keyword>
                    elseif (strpos($line, '!KEYWORDS ') === 0) {
                        $keywords = explode(', ', preg_replace('/^!KEYWORDS /', '', $line));
                    }
                    // 0 Name: <Filename>.dat
                    elseif (strpos($line, 'Name: ') === 0) {
                        $model->setNumber(preg_replace('/(^Name: )(.*)(.dat)/', '$2', $line));
                    }
                    // 0 Author: <Realname> [<Username>]
                    elseif (strpos($line, 'Author: ') === 0) {
                        $model->setAuthor(preg_replace('/^Author: /', '', $line));
                    }
                }
            }

            $cat = $this->em->getRepository('AppBundle:Category')->findOneBy(['name' => $category]);
            if ($cat == null) {
                $cat = new Category();
                $cat->setName($category);
            }

            $model->setCategory($cat);
            $cat->addModel($model);
        } else {
            throw new LogicException('loadHeader error'); //TODO
        }
        fclose($handle);

        return $model;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return \League\Flysystem\File
     */
    private function createStlFile($file)
    {
        $stlFilename = str_replace('.dat', '.stl', $file->getFilename());

        if (!$this->dataPath->has($stlFilename)) {
            $builder = new ProcessBuilder();
            $process = $builder
                ->setPrefix($this->ldview)
                ->setArguments([
//                $this->ldraw->getAdapter()->getPathPrefix().$file['path'],
                    $file->getRealPath(),
                    '-LDrawDir='.$this->ldraw->getAdapter()->getPathPrefix(),
                    '-ExportFile='.$this->dataPath->getAdapter()->getPathPrefix().$stlFilename,
                ])
                ->getProcess();

            $process->run();

            if (!$process->isSuccessful() || !$this->dataPath->has($stlFilename)) {
                throw new LogicException($file->getFilename().' : '.$process->getOutput()); //TODO
            }
        }

        return $this->dataPath->get($stlFilename);
    }
}
