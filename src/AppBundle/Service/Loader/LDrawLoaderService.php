<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Manager\LDrawManager;
use AppBundle\Service\LDrawService;
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
    private $ldraw;

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
     * @param array $ldraw_url
     */
    public function __construct(LDViewService $LDViewService, $ldraw_url, LDrawManager $ldrawService, $datParser)
    {
        $this->LDViewService = $LDViewService;
        $this->ldraw_url = $ldraw_url;
        $this->ldrawService = $ldrawService;
        $this->datParser = $datParser;
    }

    /**
     * Download current LDraw library and extract it to system tmp directory.
     *
     * @return string Absolute path to temporary Ldraw library
     */
    public function downloadLibrary()
    {
        $temp = $this->downloadFile($this->ldraw_url);

        // Create unique temporary directory
        $temp_dir = tempnam(sys_get_temp_dir(), 'printabrick.');
        mkdir($temp_dir);

        // Unzip downloaded library zip file to temporary directory
        $zip = new \ZipArchive();
        if ($zip->open($temp) != 'true') {
            throw new LogicException('Error :- Unable to open the Zip File');
        }
        $zip->extractTo($temp_dir);
        $zip->close();

        // Unlink downloaded zip file
        unlink($temp);

        return $temp_dir;
    }

    /**
     * @param $LDrawLibrary
     */
    public function loadData($LDrawLibrary)
    {
        $adapter = new Local($LDrawLibrary);
        $this->ldraw = new Filesystem($adapter);

        $this->LDViewService->setLdrawFilesystem($this->ldraw);

        $this->loadParts();
    }

    // TODO refactor

    public function loadParts()
    {
        $files = $this->ldraw->get('parts')->getContents();

        $this->initProgressBar(count($files));

        foreach ($files as $file) {
            if ($file['type'] == 'file' && $file['extension'] == 'dat') {
                $this->loadModel($file);
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
    private function loadModel($file)
    {
        $modelManager = $this->ldrawService->getModelManager();
        $subpartManager = $this->ldrawService->getSubpartManager();
        $aliasManager = $this->ldrawService->getAliasManager();

        $header = $this->datParser->parse($this->ldraw->get($file['path']));

        if ($this->isModelIncluded($header)) {
            if (isset($header['parent']) && ($parent = $this->getModelParent($header['parent'])) && ($parentFile = $this->getModelFile($parent))) {
                $parentHeader = $this->datParser->parse($parentFile);

                if ($this->isModelIncluded($parentHeader)) {
                    $model = $modelManager->create($parentHeader['id']);
                    $alias = $aliasManager->create($header['id'], $model);
                    $aliasManager->getRepository()->save($alias);

                    return $model;
                }
            } else {
                $model = $modelManager->create($header['id']);
                $model->setName($header['name']);
                $model
                    ->setCategory($this->ldrawService->getCategoryManager()->create($header['category']))
                    ->setType($this->ldrawService->getTypeManager()->create($header['type']));

                if (isset($header['keywords'])) {
                    foreach ($header['keywords'] as $keyword) {
                        $keyword = stripslashes(strtolower(trim($keyword)));
                        $model->addKeyword($this->ldrawService->getKeywordManager()->create($keyword));
                    }
                }

                if (isset($header['subparts'])) {
                    $model->setType($this->ldrawService->getTypeManager()->create('Shortcut'));

                    foreach ($header['subparts'] as $subpart) {
                        $subpart = $this->getModelParent($subpart);

                        if ($subpartFile = $this->getModelFile($subpart)) {
                            $subpartHeader = $this->datParser->parse($subpartFile);

                            if ($this->isModelIncluded($subpartHeader)) {
                                $subpartModel = $modelManager->create($subpartHeader['id']);

                                $subpart = $subpartManager->create($model, $subpartModel);
                                $subpartManager->getRepository()->save($subpart);
                            }
                        }
                    }
                }

                $model->setAuthor($header['author']);
                $model->setModified($header['modified']);
                $model->setPath($this->loadStlModel($file));

                $modelManager->getRepository()->save($model);

                return $model;
            }
        }

        return null;
    }

    /**
     * Find model file on ldraw filesystem.
     *
     * @param $id
     *
     * @return \League\Flysystem\Directory|File|\League\Flysystem\Handler|null
     */
    private function getModelFile($id)
    {
        $path = 'parts/'.strtolower($id).'.dat';
        if ($this->ldraw->has($path)) {
            return $this->ldraw->get($path);
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
        if (($file = $this->getModelFile($number)) == null) {
            return $number;
        }

        $header = $this->datParser->parse($file);

        do {
            $parent = isset($header['parent']) ? $header['parent'] : null;

            if ($file = $this->getModelFile($parent)) {
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
            && !(($header['type'] == 'Printed') && $this->getModelFile($header['parent']))
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
            return $this->LDViewService->datToStl($file, $this->ldraw)->getPath();
        } catch (\Exception $e) {
            throw $e; //TODO
        }
    }
}
