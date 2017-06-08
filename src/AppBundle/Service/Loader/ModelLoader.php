<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Author;
use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Exception\ConvertingFailedException;
use AppBundle\Exception\FileException;
use AppBundle\Exception\Loader\MissingContextException;
use AppBundle\Exception\ParseErrorException;
use AppBundle\Repository\LDraw\ModelRepository;
use AppBundle\Service\Stl\StlConverterService;
use AppBundle\Util\LDModelParser;
use AppBundle\Util\RelationMapper;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Exception;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;

class ModelLoader extends BaseLoader
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
     * @var StlConverterService
     */
    private $stlConverter;

    /** @var LDModelParser */
    private $ldModelParser;

    /** @var RelationMapper */
    private $relationMapper;

    /** @var string */
    private $LDLibraryUrl;

    /** @var bool */
    private $rewrite = false;

    /**
     * ModelLoader constructor.
     *
     * @param EntityManagerInterface $em
     * @param LoggerInterface        $logger
     * @param StlConverterService    $stlConverter
     * @param RelationMapper         $relationMapper
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, StlConverterService $stlConverter, RelationMapper $relationMapper)
    {
        $this->stlConverter = $stlConverter;
        $this->relationMapper = $relationMapper;
        $this->ldModelParser = new LDModelParser();

        parent::__construct($em, $logger);
    }

    /**
     * @param bool $rewrite
     */
    public function setRewrite($rewrite)
    {
        $this->rewrite = $rewrite;
    }

    /**
     * @param string $ldrawLibrary
     */
    public function setLDrawLibraryContext($ldrawLibrary)
    {
        try {
            $adapter = new Local($ldrawLibrary);
            $this->ldrawLibraryContext = new Filesystem($adapter);
            $this->stlConverter->setLDrawLibraryContext($this->ldrawLibraryContext);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    public function downloadLibrary($url)
    {
        $this->writeOutput([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            '<fg=cyan>Downloading LDraw library</>',
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        $libraryZip = $this->downloadFile($url);

        $temp_dir = tempnam(sys_get_temp_dir(), 'printabrick.');
        if (file_exists($temp_dir)) {
            unlink($temp_dir);
        }
        mkdir($temp_dir);

        $zip = new \ZipArchive();
        if ($zip->open($libraryZip) != 'true') {
            echo 'Error :- Unable to open the Zip File';
        }
        $zip->extractTo($temp_dir);
        $zip->close();
        unlink($libraryZip);

        $this->writeOutput(['<info>LDraw libary downloaded</info>']);

        return $temp_dir;
    }

    /**
     * Load one model into database.
     *
     * @param string $file Absolute filepath of model to load
     */
    public function loadOne($file)
    {
        if (!$this->ldrawLibraryContext) {
            throw new MissingContextException('LDrawLibrary');
        }

        $connection = $this->em->getConnection();
        try {
            $connection->beginTransaction();

            $this->getFileContext($file);
            $this->loadModel($file);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Load all models from ldraw library context into database.
     */
    public function loadAll()
    {
        if (!$this->ldrawLibraryContext) {
            throw new MissingContextException('LDrawLibrary');
        }

        $this->writeOutput([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            "<fg=cyan>Loading models from LDraw library:</> <comment>{$this->ldrawLibraryContext->getAdapter()->getPathPrefix()}</comment>",
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        $files = $this->ldrawLibraryContext->listContents('parts', false);
        $this->initProgressBar(count($files));

        $index = 0;
        $connection = $this->em->getConnection();

        foreach ($files as $file) {
            $this->progressBar->setMessage($file['basename']);

            if ($file['type'] == 'file' && $file['extension'] == 'dat') {
                $connection->beginTransaction();
                try {
                    $this->loadModel($this->ldrawLibraryContext->getAdapter()->getPathPrefix().$file['path']);

                    // clear managed objects to avoid memory issues
                    if($index++ % 50 == 0) {
                        $this->em->clear();
                    }
                    $connection->commit();
                } catch (\Exception $exception) {
                    $connection->rollBack();
                    $this->logger->error($exception->getMessage());
                }
            }

            $this->progressBar->advance();
        }

        $connection->close();
        $this->progressBar->finish();
    }

    /**
     * Load model entity and all related submodels into database while generating stl file of model.
     * Uses LDView to convert LDraw .dat to .stl.
     *
     * @param $file
     *
     * @return Model|null|false
     */
    public function loadModel($file)
    {
        /** @var ModelRepository $modelRepository */
        $modelRepository = $this->em->getRepository(Model::class);

        // Return model from database if rewrite is not enabled
        if (!$this->rewrite && $model = $modelRepository->find(basename($file, '.dat'))) {
            return $model;
        }

        try {
            $modelArray = $this->ldModelParser->parse(file_get_contents($file));
        } catch (ParseErrorException $e) {
            $this->logger->error($e->getMessage(), [$file]);

            return null;
        } catch (FileException $e) {
            $this->logger->error($e->getMessage(), [$file]);

            return null;
        }

        // Check if model fulfills rules and should be loaded
        if ($this->isModelIncluded($modelArray)) {
            // Recursively load model parent (if any) and add model id as alias of parent
            if (($parentId = $this->getParentId($modelArray)) && ($parentModelFile = $this->findSubmodelFile($parentId)) !== null) {
                if ($parentModel = $this->loadModel($parentModelFile)) {
                    // Remove old model if ~moved to
                    if ($this->rewrite && ($old = $modelRepository->find($modelArray['id'])) != null) {
                        $modelRepository->delete($old);
                    }

                    $alias = $this->em->getRepository(Alias::class)->getOrCreate($modelArray['id'], $parentModel);
                    $parentModel->addAlias($alias);

                    $modelRepository->save($parentModel);
                } else {
                    $this->logger->info('Model skipped. ', ['number' => $modelArray['id'], 'parent' => $modelArray['parent']]);
                }

                return $parentModel;
            }

            // Load model
            $model = $modelRepository->getOrCreate($modelArray['id']);

            // Recursively load models of subparts
            if (isset($modelArray['subparts'])) {
                foreach ($modelArray['subparts'] as $subpartId => $colors) {
                    foreach ($colors as $color => $count) {
                        // Try to find model of subpart
                        if (($subpartFile = $this->findSubmodelFile($subpartId)) !== null) {
                            $subModel = $this->loadModel($subpartFile);
                            if ($subModel) {
                                $subpart = $this->em->getRepository(Subpart::class)->getOrCreate($model, $subModel, $count, $color);
                                $model->addSubpart($subpart);
                            }
                        } else {
                            $this->logger->error('Subpart file not found', ['subpart' => $subpartId, 'model' => $modelArray]);
                        }
                    }
                }
            }

            // Add Keywords to model
            if (isset($modelArray['keywords'])) {
                foreach ($modelArray['keywords'] as $keyword) {
                    $keyword = $this->em->getRepository(Keyword::class)->getOrCreate(stripslashes(strtolower(trim($keyword))));
                    $model->addKeyword($keyword);
                }
            }

            try {
                // update model only if newer version
                if (!$model->getModified() || ($model->getModified() < $modelArray['modified'])) {
                    $stl = $this->stlConverter->datToStl($file, $this->rewrite)->getPath();

                    $model->setPath($stl);

                    $model
                        ->setName($modelArray['name'])
                        ->setCategory($this->em->getRepository(Category::class)->getOrCreate($modelArray['category']))
                        ->setAuthor($this->em->getRepository(Author::class)->getOrCreate($modelArray['author']))
                        ->setModified($modelArray['modified']);

                    $modelRepository->save($model);
                }
            } catch (ConvertingFailedException $e) {
                $this->logger->error($e->getMessage());

                return null;
            }

            return $model;
        }

        return false;
    }

    /**
     * Get parent id of model from alias_model.yml if defined or return parent id loaded by LDModelParser.
     *
     * Used to eliminate duplicites of models in library, that could not be determined automatically
     *
     * @param $modelArray
     *
     * @return string
     */
    private function getParentId($modelArray)
    {
        if ($this->relationMapper->find($modelArray['id'], 'alias_model') !== $modelArray['id']) {
            return $this->relationMapper->find($modelArray['id'], 'alias_model');
        }

        return $modelArray['parent'];
    }

    /**
     * Find submodel file inside model context or inside LDraw library.
     *
     *
     * LDraw.org Standards: File Format 1.0.2 (http://www.ldraw.org/article/218.html)
     *
     *  "Sub-files can be located in the LDRAW\PARTS sub-directory, the LDRAW\P sub-directory, the LDRAW\MODELS sub-directory,
     *  the current file's directory, a path relative to one of these directories, or a full path may be specified. Sub-parts are typically
     *  stored in the LDRAW\PARTS\S sub-directory and so are referenced as s\subpart.dat, while hi-res primitives are stored in the
     *  LDRAW\P\48 sub-directory and so referenced as 48\hires.dat"
     *
     *
     * @param $id
     * @param Filesystem $context
     *
     * @return string
     */
    private function findSubmodelFile($id)
    {
        // Replace "\" directory separator used inside ldraw model files with system directory separator
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($id).'.dat');

        // Try to find model in current file's directory
        if ($this->fileContext && $this->fileContext->has($filename)) {
            return $this->fileContext->getAdapter()->getPathPrefix().$filename;
        } elseif ($this->ldrawLibraryContext) {
            // Try to find model in current LDRAW\PARTS sub-directory
            if ($this->ldrawLibraryContext->has('parts'.DIRECTORY_SEPARATOR.$filename)) {
                return $this->ldrawLibraryContext->getAdapter()->getPathPrefix().'parts'.DIRECTORY_SEPARATOR.$filename;
            }
            // Try to find model in current LDRAW\P sub-directory
            elseif ($this->ldrawLibraryContext->has('p'.DIRECTORY_SEPARATOR.$filename)) {
                return $this->ldrawLibraryContext->getAdapter()->getPathPrefix().'p'.DIRECTORY_SEPARATOR.$filename;
            }
        }

        return null;
    }

    /**
     * Get new filesystem context of current file.
     *
     * @param $file
     *
     * @return Filesystem
     */
    private function getFileContext($file)
    {
        try {
            $adapter = new Local(dirname($file));
            $this->fileContext = new Filesystem($adapter);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());

            return null;
        }
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
        // Do not include part primitives and subparts
        if (in_array($modelArray['type'], ['48_Primitive', '8_Primitive', 'Primitive', 'Subpart'])) {
            return false;
        }
        // Do not include Pov-RAY file
        elseif ($modelArray['category'] == 'Pov-RAY') {
            return false;
        }
        // Do not include sticker models
        elseif ($modelArray['type'] == 'Sticker') {
            $this->logger->info('Model skipped.', ['number' => $modelArray['id'], 'type' => $modelArray['type']]);

            return false;
        }
        // Do not include models without permission to redistribute
        elseif ($modelArray['license'] != 'Redistributable under CCAL version 2.0') {
            $this->logger->info('Model skipped.', ['number' => $modelArray['id'], 'license' => $modelArray['license']]);

            return false;
        }

        return true;
    }
}
