<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Exception\ConvertingFailedException;
use AppBundle\Manager\LDrawManager;
use AppBundle\Service\LDViewService;
use AppBundle\Utils\DatParser;
use AppBundle\Utils\RelationMapper;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

//TODO refactor
class ModelLoaderService extends BaseLoaderService
{
    /**
     * @var Filesystem
     */
    private $ldrawLibraryContext;

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

    /** @var Finder */
    private $finder;

    /**
     * LDrawLoaderService constructor.
     * @param LDViewService $LDViewService
     * @param LDrawManager $ldrawService
     * @param RelationMapper $relationMapper
     */
    public function __construct($LDViewService, $ldrawService, $relationMapper)
    {
        $this->LDViewService = $LDViewService;
        $this->ldrawService = $ldrawService;
        $this->relationMapper = $relationMapper;

        $this->datParser = new DatParser();
        $this->finder = new Finder();
    }

    public function setLDrawLibraryContext($ldrawLibrary)
    {
        $adapter = new Local($ldrawLibrary);
        $this->ldrawLibraryContext = new Filesystem($adapter);

        $this->LDViewService->setLdrawFilesystem($this->ldrawLibraryContext);
    }

    public function loadFileContext($file) {
        $adapter = new Local($file);
        $this->fileContext = new Filesystem($adapter);
    }

    public function loadAllModels()
    {
        $files = $this->finder->in(['/home/hubnedav/Documents/ldraw'])->path('parts/')->name('*.dat')->depth(1)->files();

        $modelManager = $this->ldrawService->getModelManager();

        $this->initProgressBar($files->count());

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $this->newModels = [];

            $model = $this->loadModel($file->getRealPath());

            if($model !== null) {
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
        $model = null;

        $modelManager = $this->ldrawService->getModelManager();
        $subpartManager = $this->ldrawService->getSubpartManager();

        if(($model = $modelManager->findByNumber(basename($file,'.dat'))) || ($model = isset($this->newModels[basename($file,'.dat')]) ? $this->newModels[basename($file,'.dat')] : null)) {
            return $model;
        }

        try {
            $modelArray = $this->datParser->parse($file);
        } catch (\Exception $e) {
            dump($e->getMessage());
            return null;
        }

        if ($this->isModelIncluded($modelArray)) {

            if ($parentModelFile = $this->getParentModelFile($modelArray)) {
                try {
                    if(($parentModel = $this->loadModel($parentModelFile))!= null) {
                        $alias = $this->ldrawService->getAliasManager()->create($modelArray['id'], $parentModel);
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
                $model = $modelManager->create($modelArray['id']);

                if (isset($modelArray['keywords'])) {
                    foreach ($modelArray['keywords'] as $keyword) {
                        $keyword = stripslashes(strtolower(trim($keyword)));
                        $model->addKeyword($this->ldrawService->getKeywordManager()->create($keyword));
                    }
                }

                if (isset($modelArray['subparts'])) {
                    foreach ($modelArray['subparts'] as $subpartId => $count) {
                        if(strpos($subpartId, 's\\') === false) {
                            if(($subpartFile = $this->findModelFile($subpartId)) != null) {
                                try {
                                    if ($subModel = $this->loadModel($subpartFile)) {
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

                $model
                    ->setName($modelArray['name'])
                    ->setCategory($this->ldrawService->getCategoryManager()->create($modelArray['category']))
                    ->setAuthor($modelArray['author'])
                    ->setModified($modelArray['modified'])
                    ->setPath($this->loadStlModel($file));

                $this->LDViewService->datToPng($file);

                $this->newModels[$model->getNumber()] = $model;
            }
        }

        return $model;
    }

    private function getParentModelFile($modelArray) {
        if($this->relationMapper->find($modelArray['id'], 'alias_model') !== $modelArray['id']) {
            return $this->findModelFile($this->relationMapper->find($modelArray['id'], 'alias_model'));
        } else {
            return strpos($modelArray['parent'], 's\\') === false ? $this->findModelFile($modelArray['parent']) : null;
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
        $filename = strtolower($id).'.dat';

        if($this->fileContext && $this->fileContext->has($filename)) {
            return $this->fileContext->getAdapter()->getPathPrefix().$filename;
        } else if ($this->ldrawLibraryContext->has('parts/'.$filename)) {
            return $this->ldrawLibraryContext->getAdapter()->getPathPrefix().'parts/'.$filename;
        }

        return null;
    }

    /**
     * Determine if model file should be loaded into database.
     *
     * @param $modelArray
     *
     * @return bool
     */
    private function isModelIncluded($modelArray)
    {
        // Do not include sticker parts and incomplete parts
        if ( $modelArray['type'] != 'Subpart' && $modelArray['type'] != 'Sticker' ) {
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
        } catch (ConvertingFailedException $e) {
            throw $e; //TODO
        }
    }
}
