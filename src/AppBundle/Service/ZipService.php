<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use League\Flysystem\Filesystem;
use ZipStream\ZipStream;

class ZipService
{
    /** @var ZipStream */
    private $archive;

    /** @var Filesystem */
    private $mediaFilesystem;

    /** @var SetService */
    private $setService;

    /**
     * ZipService constructor.
     *
     * @param $mediaFilesystem
     * @param $setService
     */
    public function __construct($mediaFilesystem, $setService)
    {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->setService = $setService;
    }

    public function createFromSet(Set $set, $sorted = false)
    {
        $sort = $sorted ? 'sorted' : 'unsorted';

        $filename = "set_{$set->getNumber()}_{$set->getName()}({$sort}).zip";

        // Initialize zip stream
        $this->archive = new ZipStream($filename);

        if ($sorted) {
            $this->addSetGroupedByColor($set);
        } else {
            $this->addSet($set);
        }

        $this->archive->finish();

        return $this->archive;
    }

    public function createFromModel(Model $model, $subparts = false)
    {
        $filename = "model_{$model->getNumber()}.zip";

        // Initialize zip stream
        $this->archive = new ZipStream($filename);

        $this->addModel($model);

        $this->archive->finish();

        return $this->archive;
    }

    /**
     * Add stl files of models used in set into zip grouped by color.
     *
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     */
    public function addSetGroupedByColor(Set $set, $spare = null)
    {
        $colors = $this->setService->getModelsGroupedByColor($set, $spare);

        foreach ($colors as $colorArray) {
            $models = $colorArray['models'];
            $color = $colorArray['color'];
            foreach ($models as $modelArray) {
                $model = $modelArray['model'];
                $quantity = $modelArray['quantity'];

                $filename = "{$color->getName()}/{$model->getNumber()}_({$quantity}x).stl";

                $this->archive->addFile($filename, $this->mediaFilesystem->read($model->getPath()));
            }
        }
    }

    /**
     * Add stl files used in set into zip.
     *
     * @param Set  $set
     * @param bool $spare If true - add only spare parts, false - add only regular parts, null - add all parts
     */
    public function addSet(Set $set, $spare = null)
    {
        $models = $this->setService->getModels($set, $spare);

        foreach ($models as $number => $array) {
            $quantity = $array['quantity'];
            $filename = $number."_({$quantity}x).stl";

            $this->archive->addFile($filename, $this->mediaFilesystem->read($array['model']->getPath()));
        }
    }

    public function addModel(Model $model, $count = 1, $folder = '')
    {
        $filename = $folder.$model->getNumber()."_({$count}x).stl";

        $this->archive->addFile($filename, $this->mediaFilesystem->read($model->getPath()));

        foreach ($model->getSubparts() as $subpart) {
            $this->addModel($subpart->getSubpart(), $subpart->getCount(), $folder.$model->getNumber().'_subparts/');
        }
    }

    // TODO add licence file and information to zip file
    public function addLicence()
    {
    }
}
