<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class ZipService
{
    /** @var \ZipArchive */
    private $archive;

    /** @var Filesystem */
    private $mediaFilesystem;

    /** @var SetService */
    private $setService;

    /** @var ModelService */
    private $modelService;

    /** @var string */
    private $zipName;

    /** @var array */
    private $models = [];

    /**
     * ZipService constructor.
     *
     * @param FilesystemInterface $mediaFilesystem
     * @param ModelService        $modelService
     * @param SetService          $setService
     */
    public function __construct(FilesystemInterface $mediaFilesystem, ModelService $modelService, SetService $setService)
    {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->setService = $setService;
        $this->modelService = $modelService;
    }

    private function createZip($path)
    {
        $archive = new \ZipArchive();
        $archive->open($path, \ZipArchive::CREATE);

        return $archive;
    }

    /**
     * Create zip archive with models in set in temp dir.
     *
     * @param Set    $set
     * @param string $filename Filename of archive base directory
     * @param bool   $sorted   Sort models into folders by color
     *
     * @return bool|string
     */
    public function createFromSet(Set $set, $filename, $sorted = false)
    {
        $this->zipName = $filename;

        $zipPath = tempnam(sys_get_temp_dir(), 'printabrick');
        $this->archive = $this->createZip($zipPath);

        if ($sorted) {
            $this->addSetGroupedByColor($set);
        } else {
            $this->addSet($set);
        }

        $this->addLicense();
        $this->archive->close();

        return $zipPath;
    }

    /**
     * Create zip archive of model in temp dir.
     *
     * @param Model  $model
     * @param string $filename Filename of archive base directory
     * @param bool   $subparts Include directory with subparts into archive
     *
     * @return bool|string
     */
    public function createFromModel(Model $model, $filename, $subparts = false)
    {
        $this->zipName = $filename;

        $zipPath = tempnam(sys_get_temp_dir(), 'printabrick');
        $this->archive = $this->createZip($zipPath);

        $filename = "{$this->zipName}/{$model->getId()}.stl";
        $this->addModel($filename, $model);

        if ($subparts) {
            foreach ($this->modelService->getSubmodels($model) as $subpart) {
                $submodel = $subpart['model'];
                $filename = "{$this->zipName}/submodels/{$submodel->getId()}_({$subpart['quantity']}x).stl";

                $this->addModel($filename, $submodel);
            }
        }

        $this->addLicense();
        $this->archive->close();

        return $zipPath;
    }

    /**
     * Add stl files of models used in set into zip grouped by color.
     *
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     */
    private function addSetGroupedByColor(Set $set, $spare = null)
    {
        $colors = $this->setService->getModelsGroupedByColor($set, $spare);

        foreach ($colors as $colorArray) {
            $models = $colorArray['models'];
            $color = $colorArray['color'];
            foreach ($models as $modelArray) {
                $model = $modelArray['model'];
                $quantity = $modelArray['quantity'];

                $filename = "{$this->zipName}/{$color->getName()}_(#{$color->getRgb()})/{$model->getId()}_({$quantity}x).stl";

                $this->addModel($filename, $model);
            }
        }
    }

    /**
     * Add stl files used in set into zip.
     *
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     */
    private function addSet(Set $set, $spare = null)
    {
        $models = $this->setService->getModels($set, $spare);

        foreach ($models as $number => $array) {
            $model = $array['model'];
            $quantity = $array['quantity'];
            $filename = "{$this->zipName}/{$number}_({$quantity}x).stl";
            $this->models[$number] = $array['model'];

            $this->addModel($filename, $model);
        }
    }

    private function addModel($path, $model)
    {
        $this->archive->addFromString($path, $this->mediaFilesystem->read($model->getPath()));
        $this->models[$model->getId()] = $model;
    }

    /**
     * Add LICENSE.txt file to archive.
     */
    private function addLicense()
    {
        $text = sprintf('All stl files in this archive were converted by LDView from official LDraw Library http://www.ldraw.org/'."\n\n");
        $text .= sprintf('Files are licensed under the Creative Commons - Attribution license.'."\n");
        $text .= sprintf('http://creativecommons.org/licenses/by/2.0/'."\n\n");

        $text .= sprintf('Attribution:'."\n"."\n");

        foreach ($this->models as $model) {
            $text .= sprintf('%s - "%s" by %s'."\n", $model->getId(), $model->getName(), $model->getAuthor()->getName());
        }

        $this->archive->addFromString("{$this->zipName}/LICENSE.txt", $text);
    }
}
