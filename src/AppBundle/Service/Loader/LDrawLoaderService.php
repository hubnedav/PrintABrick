<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Manager\LDrawManager;
use AppBundle\Service\LDViewService;
use AppBundle\Utils\DatParser;
use AppBundle\Utils\RelationMapper;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

//use Symfony\Component\Asset\Exception\LogicException;

//TODO refactor
class LDrawLoaderService extends BaseLoaderService
{
    /**
     * @var Filesystem
     */
    private $ldrawLibrary;

    /**
     * @var Filesystem
     */
    private $fileContext;

    /**
     * @var LDViewService
     */
    private $LDViewService;

    /** @var LDrawManager */
    private $ldrawService;

    /** @var DatParser */
    private $datParser;

    private $newModels;

    /** @var RelationMapper */
    protected $relationMapper;

    /**
     * LDrawLoaderService constructor.
     * @param Filesystem $ldrawLibraryFilesystem
     * @param LDViewService $LDViewService
     * @param LDrawManager $ldrawService
     * @param RelationMapper $relationMapper
     */
    public function __construct($ldrawLibraryFilesystem, $LDViewService, $ldrawService, $relationMapper)
    {
        $this->LDViewService = $LDViewService;
        $this->ldrawService = $ldrawService;
        $this->datParser = new DatParser();
        $this->ldrawLibrary = $ldrawLibraryFilesystem;
        $this->relationMapper = $relationMapper;
    }

    private function dumpModel($model, $level = 1) {
        $level = $level + 1;
        dump(str_repeat("-", 2*$level).'> '.$model->getNumber());
        foreach ($model->getSubparts() as $subpart) {
            $this->dumpModel($subpart->getSubpart(), $level);
        }
    }

    public function loadAllModels()
    {
        $files = $this->ldrawLibrary->get('parts')->getContents();
        $modelManager = $this->ldrawService->getModelManager();

        $this->initProgressBar(count($files));

        foreach ($files as $file) {
            if ($file['type'] == 'file' && $file['extension'] == 'dat') {

                $this->newModels = [];

                $model = $this->loadModel($this->ldrawLibrary->getAdapter()->getPathPrefix().$file['path']);

                if($model !== null) {
//                    dump($model->getNumber());
                    try {
                        $modelManager->getRepository()->save($model);
                    } catch (\Exception $exception) {
                        dump($exception);
//                        dump($model);

                        $this->dumpModel($model);

                        exit();
                    }
                }
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
        $model = null;

        $modelManager = $this->ldrawService->getModelManager();
        $subpartManager = $this->ldrawService->getSubpartManager();

        if(($model = $modelManager->findByNumber(basename($file,'.dat'))) || ($model = isset($this->newModels[basename($file,'.dat')]) ? $this->newModels[basename($file,'.dat')] : null)) {
            $this->LDViewService->datToPng($file);
            return $model;
        }

        try {
            $header = $this->datParser->parse($file);
        } catch (\Exception $e) {
            dump($e);
            return null;
        }

        if ($this->isModelIncluded($header)) {

            if($this->relationMapper->find($header['id'], 'alias_model') !== $header['id']) {
                $parentFile = $this->findModelFile($this->relationMapper->find($header['id'], 'alias_model'));
            } else {
                $parentFile = isset($header['parent']) && strpos($header['parent'], 's\\') === false ? $this->findModelFile($header['parent']) : null;
            }

            if ($parentFile) {
                try {
                    $parentModel = $this->loadModel($parentFile);

                    if($parentModel) {
                        $alias = new Alias();
                        $alias->setNumber($header['id']);
                        $alias->setModel($parentModel);
                        $parentModel->addAlias($alias);

                        $this->newModels[$parentModel->getNumber()] = $parentModel;
                        return $parentModel;
                    }
                } catch (\Exception $e) {
                    dump('b');
                    dump($e->getMessage());
                    return null;
                }
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
                    foreach ($header['subparts'] as $subpartId => $count) {
                        if(strpos($subpartId, 's\\') === false) {
                            if(($subpartFile = $this->findModelFile($subpartId)) != null) {
                                try {
                                    $subModel = $this->loadModel($subpartFile);

                                    if ($subModel) {
                                        $subpart = $subpartManager->create($model,$subModel,$count);
                                        $model->addSubpart($subpart);
                                    }
                                } catch (\Exception $e) {
                                    dump('c');
                                    dump($e->getMessage());
                                }
                            }
                        }
                    }
                }

                $model->setAuthor($header['author']);
                $model->setModified($header['modified']);
                $model->setPath($this->loadStlModel($file));

                $this->newModels[$model->getNumber()] = $model;
            }
        }

        return $model;
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
        $filename = strtolower($id).'.dat';

        if($this->fileContext && $this->fileContext->has($filename)) {
            return $this->fileContext->getAdapter()->getPathPrefix().$filename;
        } else if ($this->ldrawLibrary->has('parts/'.$filename)) {
            return $this->ldrawLibrary->getAdapter()->getPathPrefix().'parts/'.$filename;
        }

        return null;
    }

    public function loadFileContext($file) {
        $adapter = new Local(dirname(realpath($file)));
        $this->fileContext = new Filesystem($adapter);
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
            $header['type'] != 'Subpart'
            && $header['type'] != 'Sticker'
//            && !(($header['type'] == 'Printed') && $this->findModelFile($header['parent']))
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
