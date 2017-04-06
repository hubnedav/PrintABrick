<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Exception\FileNotFoundException;
use AppBundle\Manager\LDrawManager;
use AppBundle\Service\LDViewService;
use AppBundle\Utils\DatParser;
use League\Flysystem\Adapter\Local;
use League\Flysystem\File;
use League\Flysystem\Filesystem;
//use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Console\Helper\ProgressBar;

//TODO refactor
class LDrawLoaderService extends BaseLoaderService
{
    /**
     * @var Filesystem
     */
    private $ldrawLibraryFilesystem;

    /**
     * @var string download URL with current LDraw library
     */
    private $ldraw_url;

    /**
     * @var LDViewService
     */
    private $LDViewService;

    /** @var LDrawManager */
    private $ldrawService;

    /** @var DatParser */
    private $datParser;

    /**
     * LDrawLoaderService constructor.
     * @param Filesystem $ldrawLibraryFilesystem
     * @param LDViewService $LDViewService
     * @param $ldraw_url
     * @param LDrawManager $ldrawService
     * @param DatParser $datParser
     */
    public function __construct($ldrawLibraryFilesystem, $LDViewService, $ldraw_url, $ldrawService, $datParser)
    {
        $this->LDViewService = $LDViewService;
        $this->ldraw_url = $ldraw_url;
        $this->ldrawService = $ldrawService;
        $this->datParser = $datParser;
        $this->ldrawLibraryFilesystem = $ldrawLibraryFilesystem;
    }

    public function loadAllModels()
    {
        $files = $this->ldrawLibraryFilesystem->get('parts')->getContents();
        $modelManager = $this->ldrawService->getModelManager();

        $this->initProgressBar(count($files));

        foreach ($files as $file) {
            if ($file['type'] == 'file' && $file['extension'] == 'dat') {
                $model = $this->loadModel($this->ldrawLibraryFilesystem->getAdapter()->getPathPrefix().$file['path']);

                if($model)
                    $modelManager->getRepository()->save($model);
            }

            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Load Model entity into database.
     *
     * @param $file
     *
     * @return Model|null
     */
    public function loadModel($file)
    {
        $modelManager = $this->ldrawService->getModelManager();
        $subpartManager = $this->ldrawService->getSubpartManager();
        $aliasManager = $this->ldrawService->getAliasManager();

        if($model = $modelManager->findByNumber(basename($file,'.dat'))) {
            return $model;
        }

        $header = $this->datParser->parse($file);

        if ($this->isModelIncluded($header)) {
            if (isset($header['parent']) && ($parent = $this->getModelParent($header['parent'])) && ($parentFile = $this->findModelFile($parent))) {
                $parentModel = $this->loadModel($parentFile);

                if ($parentModel) {
                    $alias = $aliasManager->create($header['id'], $parentModel);
                    $aliasManager->getRepository()->save($alias);
                    $this->progressBar->advance();
                }
                return $parentModel;
            } else {
                $model = $modelManager->create($header['id']);
                $model->setName($header['name']);
                $model->setCategory($this->ldrawService->getCategoryManager()->create($header['category']));

                if (isset($header['keywords'])) {
                    foreach ($header['keywords'] as $keyword) {
                        $keyword = stripslashes(strtolower(trim($keyword)));
                        $model->addKeyword($this->ldrawService->getKeywordManager()->create($keyword));
                    }
                }

                if (isset($header['subparts'])) {
                    foreach ($header['subparts'] as $subpartId) {
                        $subpartId = $this->getModelParent($subpartId);

                        $subModel = $modelManager->getRepository()->findOneBy(['number'=>$subpartId]);

                        if(!$subModel && ($subpartFile = $this->findModelFile($subpartId)) != null) {
                            $subModel = $this->loadModel($subpartFile);
                        }

                        if ($subModel) {
                            $subpart = $subpartManager->create($model, $subModel);
                            $subpartManager->getRepository()->save($subpart);
                            $this->progressBar->advance();
                        }
                    }
                }

                $model->setAuthor($header['author']);
                $model->setModified($header['modified']);
                $model->setPath($this->loadStlModel($file));

                return $model;
            }
        }
    }

    /**
     * Find model file on ldraw filesystem.
     *
     * @param $id
     *
     * @return string
     */
    private function findModelFile($id)
    {
        $path = 'parts/'.strtolower($id).'.dat';
        if ($this->ldrawLibraryFilesystem->has($path)) {
            return $this->ldrawLibraryFilesystem->getAdapter()->getPathPrefix().$path;
        }

        return null;
    }

    /**
     * Recursively load model parent id of model with $number id.
     *
     * @param string $number
     *
     * @return string
     */
    private function getModelParent($number)
    {
        if (($file = $this->findModelFile($number)) == null) {
            return $number;
        }

        $header = $this->datParser->parse($file);

        do {
            $parent = isset($header['parent']) ? $header['parent'] : null;

            if ($file = $this->findModelFile($parent)) {
                $header = $this->datParser->parse($file);
            } else {
                break;
            }
        } while ($parent);

        return $header['id'];
    }

    /**
     * Determine if model file should be loaded into database.
     *
     * @param $header
     *
     * @return bool
     */
    private function isModelIncluded($header)
    {
        // Do not include sticker parts and incomplete parts
        if (
            strpos($header['id'], 's') !== 0
            && $header['type'] != 'Subpart'
            && $header['type'] != 'Sticker'
            && !(($header['type'] == 'Printed') && $this->findModelFile($header['parent']))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Load stl model by calling LDViewSevice and create new Model.
     *
     * @param $file
     *
     * @throws \Exception
     *
     * @return string path of stl file
     */
    private function loadStlModel($file)
    {
        try {
            return $this->LDViewService->datToStl($file)->getPath();
        } catch (\Exception $e) {
            throw $e; //TODO
        }
    }
}
