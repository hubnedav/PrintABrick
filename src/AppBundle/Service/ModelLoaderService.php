<?php

namespace AppBundle\Service;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\Category;
use AppBundle\Entity\Color;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\Model;
use AppBundle\Entity\BuildingKit;
use AppBundle\Entity\Part;
use AppBundle\Entity\Part_BuildingKit;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;


class ModelLoaderService
{
    private $STLlib;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RebrickableManager
     */
    private $rebrickableManager;

    /**
     * ModelLoaderService constructor.
     */
    public function __construct($em, $STLlib, $rebrickableManager)
    {
        $this->STLlib = $STLlib;
        $this->em = $em;
        $this->rebrickableManager = $rebrickableManager;
    }

    // LDraw

    public function loadModels($LDrawLibrary)
    {
        $finder = new Finder();
        $files = $finder->files()->name('*.dat')->depth('== 0')->in(getcwd().'/'.$LDrawLibrary.'/parts');

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
            $p['category'] = explode(' ',trim($description))[0];

            //TODO handle ~Moved to

            while (!feof($handle)) {
                $line = trim(fgets($handle));
                if ($line && ($line[0] == '1')) {
                    break;
                } elseif ($line && ($line[0] == '0' && strpos($line, '!CATEGORY '))) {
                    $p['category'] = trim(explode('!CATEGORY ', $line)[1]);
                } elseif ($line && ($line[0] == '0' && strpos($line, '!KEYWORDS '))) {
                    $keywords = explode(',',explode('!KEYWORDS ', $line)[1]);
                    foreach ($keywords as $k) {
                        $p['keywords'][] = trim($k);
                    }
                } elseif ($line && ($line[0] == '0' && strpos($line, 'Name: '))) {
                    $model->setNumber(trim(explode('.dat',explode('Name: ', $line)[1])[0]));
                } elseif ($line && ($line[0] == '0' && strpos($line, 'Author: '))) {
                    $model->setAuthor(explode('Author: ', $line)[1]);
                }
            }

            $this->em->persist($model);
            $this->em->flush();
        } else {
            throw new LogicException('loadHeader error'); //TODO
        }
        fclose($handle);
    }
    
    // Rebrickable

    public function loadPartBuildingKits($set_pieces) {
        $partRepository = $this->em->getRepository('AppBundle:Part');
        $buldingKitRepository = $this->em->getRepository('AppBundle:BuildingKit');
        $colorRepository = $this->em->getRepository('AppBundle:Color');

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        if (($handle = fopen($set_pieces, "r")) !== FALSE) {
            $header = fgetcsv($handle, 200, ",");
            $line = 0;
            while (($data = fgetcsv($handle, 200, ",")) !== FALSE) {
                $partBuildingKit = new Part_BuildingKit();

                $line = $line + 1;

                if($line%1000 == 0 ) {
                    var_dump($line);
                    $this->em->flush();
                }

                $buildingKit = $buldingKitRepository->findOneBy(['number' => $data[0]]);
                $part = $partRepository->findOneBy(['number' => $data[1]]);
                $color = $colorRepository->findOneBy(['id' => $data[3]]);

                if($part && $buildingKit) {
                    $partBuildingKit
                        ->setBuildingKit($buildingKit)
                        ->setPart($part)
                        ->setCount($data[2])
                        ->setColor($color)
                        ->setType($data[4] - 1);

                    $this->em->persist($partBuildingKit);
                }
            }

            $this->em->flush();
            fclose($handle);
        }
    }


    public function loadBuildingKits($sets_file) {
        $keywordRepository = $this->em->getRepository('AppBundle:Keyword');

        if (($handle = fopen($sets_file, "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ",");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $buildingKit = new BuildingKit();

                for($i=3; $i <=5; $i++) {
                    $keyword = $keywordRepository->findOneBy(['name' => $data[$i]]);
                    if($keyword == null) {
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

            }
            $this->em->flush();

            fclose($handle);
        }
    }

    public function loadParts($pieces_file) {
        $categoryRepository = $this->em->getRepository('AppBundle:Category');

        if (($handle = fopen($pieces_file, "r")) !== FALSE) {
            $header = fgetcsv($handle, 300, ",");
            while (($data = fgetcsv($handle, 300, ",")) !== FALSE) {
                $part = new Part();
                $part->setNumber($data[0])->setName($data[1]);


                $category = $categoryRepository->findOneBy(['name' => $data[2]]);
                if($category == null) {
                    $category = new Category();
                    $category->setName($data[2]);
                    $this->em->persist($category);
                    $this->em->flush();
                }

                $part->setCategory($category);
                $part->setModel($this->getModel($part));
                $category->addPart($part);

                $this->em->persist($part);

//                $this->em->persist($category);
            }

            $this->em->flush();

            fclose($handle);
        }
    }

    public function loadColors() {
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

    public function getModel(Part $part) {
        $modelRepository = $this->em->getRepository('AppBundle:Model');

        if(strpos($part->getNumber(), 'p')) {
            $model = $modelRepository->findOneBy(['number' => explode('p', $part->getNumber())[0]]);
        } else {
            $model = $modelRepository->findOneBy(['number' => $part->getNumber()]);
        }

        return $model;
    }
}
