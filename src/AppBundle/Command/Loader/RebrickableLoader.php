<?php

namespace AppBundle\Command\Loader;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\BuildingKit;
use AppBundle\Entity\Category;
use AppBundle\Entity\Color;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\Part;
use AppBundle\Entity\Part_BuildingKit;
use Symfony\Component\Console\Helper\ProgressBar;

//TODO Refactor
class RebrickableLoader extends Loader
{
    /**
     * @var RebrickableManager
     */
    private $rebrickableManager;

    private $rebrickable_url;

    /**
     * ModelLoaderService constructor.
     */
    public function __construct($em, $rebrickableManager, $rebrickable_url)
    {
        $this->em = $em;
        $this->rebrickableManager = $rebrickableManager;
        $this->rebrickable_url = $rebrickable_url;
    }

    public function loadPartBuildingKits()
    {
        $this->output->writeln('Downloading set_pieces.csv from Rebrickable.com');
        $file = $this->downloadFile('compress.zlib://'.$this->rebrickable_url['set_pieces']);

        $partRepository = $this->em->getRepository('AppBundle:Part');
        $buldingKitRepository = $this->em->getRepository('AppBundle:BuildingKit');
        $colorRepository = $this->em->getRepository('AppBundle:Color');

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->output->writeln('Loading set_pieces.csv into Database');
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 200, ',');

            // create a new progress bar (50 units)
            $progress = new ProgressBar($this->output, intval(exec("wc -l '$file'"))); //TODO replace wc-l
            $progress->setFormat('very_verbose');
            $progress->setBarWidth(50);
            $progress->start();

            $index = 0;
            while (($data = fgetcsv($handle, 200, ',')) !== false) {
                $partBuildingKit = new Part_BuildingKit();

                $buildingKit = $buldingKitRepository->findOneBy(['number' => $data[0]]);
                $part = $partRepository->findOneBy(['number' => $data[1]]);
                $color = $colorRepository->findOneBy(['id' => $data[3]]);

                if ($part && $buildingKit) {
                    $partBuildingKit
                        ->setBuildingKit($buildingKit)
                        ->setPart($part)
                        ->setCount($data[2])
                        ->setColor($color)
                        ->setType($data[4] - 1);

                    $this->em->persist($partBuildingKit);
                }

                $index = $index + 1;
                if ($index % 25 == 0) {
                    $this->em->flush();
                    $this->em->clear();
                }
                $progress->advance();
            }

            $this->em->flush();
            $this->em->clear();
            fclose($handle);
            $progress->finish();
        }

        unlink($file);
    }

    public function loadBuildingKits()
    {
        $this->output->writeln('Downloading sets.csv from Rebrickable.com');
        $file = $this->downloadFile('compress.zlib://'.$this->rebrickable_url['sets']);

        $keywordRepository = $this->em->getRepository('AppBundle:Keyword');

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->output->writeln('Loading sets.csv into Database');
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle, 500, ',');

            // create a new progress bar (50 units)
            $progress = new ProgressBar($this->output, intval(exec("wc -l '$file'"))); //TODO replace wc-l
            $progress->setFormat('very_verbose');
            $progress->setBarWidth(50);
            $progress->start();

            $index = 0;
            while (($data = fgetcsv($handle, 500, ',')) !== false) {
                $buildingKit = new BuildingKit();

                for ($i = 3; $i <= 5; ++$i) {
                    $keyword = $keywordRepository->findOneBy(['name' => $data[$i]]);
                    if ($keyword == null) {
                        $keyword = new Keyword();
                        $keyword->setName($data[$i]);
                        $this->em->persist($keyword);
                        $this->em->flush();
                    }

                    $buildingKit->addKeyword($keyword);
                }

                $buildingKit
                    ->setNumber($data[0])
                    ->setYear($data[1])
                    ->setPartCount($data[2])
                    ->setName($data[6]);

                $this->em->persist($buildingKit);

                $index = $index + 1;
                if ($index % 25 == 0) {
                    $this->em->flush();
                    $this->em->clear();
                }

                $progress->advance();
            }
            $this->em->flush();
            $this->em->clear();

            fclose($handle);

            $progress->finish();
        }
        unlink($file);
    }

    public function loadParts()
    {
        $this->output->writeln('Downloading pieces.csv from Rebrickable.com');
        $file = $this->downloadFile('compress.zlib://'.$this->rebrickable_url['pieces']);

        $categoryRepository = $this->em->getRepository('AppBundle:Category');

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->output->writeln('Loading pieces.csv into Database');
        if (($handle = fopen($file, 'r')) !== false) {
            // create a new progress bar (50 units)
            $progress = new ProgressBar($this->output, intval(exec("wc -l '$file'"))); //TODO replace wc-l
            $progress->setFormat('very_verbose');
            $progress->setBarWidth(50);
            $progress->start();

            $header = fgetcsv($handle, 300, ',');
            while (($data = fgetcsv($handle, 300, ',')) !== false) {
                $part = new Part();
                $part->setNumber($data[0])->setName($data[1]);

                $category = $categoryRepository->findOneBy(['name' => $data[2]]);
                if ($category == null) {
                    $category = new Category();
                    $category->setName($data[2]);
                    $this->em->persist($category);
                    $this->em->flush();
                }

                $part->setCategory($category);
                $part->setModel($this->getModel($part));
                $category->addPart($part);

                $this->em->persist($part);

                $progress->advance();
            }

            $this->em->flush();
            $this->em->clear();

            fclose($handle);

            $progress->finish();
        }

        unlink($file);
    }

    public function loadColors()
    {
        $this->output->writeln('Loading colors into Database');

        $rb_colors = $this->rebrickableManager->getColors();

        foreach ($rb_colors as $rb_color) {
            $color = new Color();
            $color
                ->setId($rb_color->getRbColorId())
                ->setName($rb_color->getColorName())
                ->setRgb($rb_color->getRgb());

            $this->em->persist($color);
        }

        $this->em->flush();
    }

    public function getModel(Part $part)
    {
        $modelRepository = $this->em->getRepository('AppBundle:Model');

        $model = $modelRepository->findOneBy(['number' => $part->getNumber()]);

        if (!$model && strpos($part->getNumber(), 'p')) {
            $model = $modelRepository->findOneBy(['number' => explode('p', $part->getNumber())[0]]);
        }

        return $model;
    }
}
