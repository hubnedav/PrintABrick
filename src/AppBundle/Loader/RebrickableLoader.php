<?php

namespace AppBundle\Loader;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\BuildingKit;
use AppBundle\Entity\Category;
use AppBundle\Entity\Color;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\Part;
use AppBundle\Entity\Part_BuildingKit;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\ProgressBar;

//TODO Refactor
class RebrickableLoader extends Loader
{
    /**
     * @var RebrickableManager
     */
    private $rebrickableManager;

    /**
     * ModelLoaderService constructor.
     */
    public function __construct($em, $rebrickableManager)
    {
        /*
         * @var $em EntityManager
         * */
        $this->em = $em;
        $this->rebrickableManager = $rebrickableManager;
    }

    public function loadPartBuildingKits()
    {
        $partRepository = $this->em->getRepository('AppBundle:Part');
        $buldingKitRepository = $this->em->getRepository('AppBundle:BuildingKit');
        $colorRepository = $this->em->getRepository('AppBundle:Color');

        $setPieces = tempnam(sys_get_temp_dir(), 'printabrick.');
        file_put_contents($setPieces, fopen('compress.zlib://http://rebrickable.com/files/set_pieces.csv.gz', 'r'));

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        if (($handle = fopen($setPieces, 'r')) !== false) {
            $header = fgetcsv($handle, 200, ',');

            // create a new progress bar (50 units)
            $progress = new ProgressBar($this->output, intval(exec("wc -l '$setPieces'"))); //TODO replace wc-l
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
            $progress->clear();
        }

        unlink($setPieces);
    }

    public function loadBuildingKits()
    {
        $keywordRepository = $this->em->getRepository('AppBundle:Keyword');

        $sets = tempnam(sys_get_temp_dir(), 'printabrick.');
        file_put_contents($sets, fopen('compress.zlib://http://rebrickable.com/files/sets.csv.gz', 'r'));

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        if (($handle = fopen($sets, 'r')) !== false) {
            $header = fgetcsv($handle, 500, ',');

            // create a new progress bar (50 units)
            $progress = new ProgressBar($this->output, intval(exec("wc -l '$sets'"))); //TODO replace wc-l
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
            $progress->clear();
        }
        unlink($sets);
    }

    public function loadParts()
    {
        $pieces = tempnam(sys_get_temp_dir(), 'printabrick.');
        file_put_contents($pieces, fopen('compress.zlib://http://rebrickable.com/files/pieces.csv.gz', 'r')); //TODO replace wc-l

        $categoryRepository = $this->em->getRepository('AppBundle:Category');

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        if (($handle = fopen($pieces, 'r')) !== false) {
            // create a new progress bar (50 units)
            $progress = new ProgressBar($this->output, intval(exec("wc -l '$pieces'")));
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
            $progress->clear();
        }

        unlink($pieces);
    }

    public function loadColors()
    {
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

        if (strpos($part->getNumber(), 'p')) {
            $model = $modelRepository->findOneBy(['number' => explode('p', $part->getNumber())[0]]);
        } else {
            $model = $modelRepository->findOneBy(['number' => $part->getNumber()]);
        }

        return $model;
    }
}
