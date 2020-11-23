<?php

namespace App\Service\Loader;

use App\Entity\Color;
use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Author;
use App\Entity\LDraw\Category;
use App\Entity\LDraw\Keyword;
use App\Entity\LDraw\Model;
use App\Entity\LDraw\ModelType;
use App\Entity\LDraw\Subpart;
use App\Exception\Loader\MissingContextException;
use App\Exception\ParseErrorException;
use App\Repository\ColorRepository;
use App\Repository\LDraw\AliasRepository;
use App\Repository\LDraw\AuthorRepository;
use App\Repository\LDraw\CategoryRepository;
use App\Repository\LDraw\KeywordRepository;
use App\Repository\LDraw\ModelRepository;
use App\Repository\LDraw\ModelTypeRepository;
use App\Repository\LDraw\SubpartRepository;
use App\Service\Stl\Exception\ConversionFailedException;
use App\Service\Stl\StlConverter;
use App\Util\LDModelParser;
use App\Util\RelationMapper;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class LdrawLoader extends LoggerAwareLoader
{
    private FilesystemInterface $ldrawFilesystem;
    private ?FilesystemInterface $fileContext = null;

    private StlConverter $stlConverter;
    private LDModelParser $ldModelParser;

    protected RelationMapper $relationMapper;

    private bool $rewrite = true;

    private EntityManagerInterface $em;
    private ModelRepository $modelRepository;
    private ModelTypeRepository $modelTypeRepository;
    private AliasRepository $aliasRepository;
    private KeywordRepository $keywordRepository;
    private CategoryRepository $categoryRepository;
    private AuthorRepository $authorRepository;
    private SubpartRepository $subpartRepository;
    private ColorRepository $colorRepository;

    /**
     * ModelLoader constructor.
     */
    public function __construct(EntityManagerInterface $em, StlConverter $stlConverter)
    {
        parent::__construct();
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger();
        $this->ldModelParser = new LDModelParser();
        $this->stlConverter = $stlConverter;

        $this->modelRepository = $this->em->getRepository(Model::class);
        $this->modelTypeRepository = $this->em->getRepository(ModelType::class);
        $this->aliasRepository = $this->em->getRepository(Alias::class);
        $this->keywordRepository = $this->em->getRepository(Keyword::class);
        $this->authorRepository = $this->em->getRepository(Author::class);
        $this->categoryRepository = $this->em->getRepository(Category::class);
        $this->subpartRepository = $this->em->getRepository(Subpart::class);
        $this->colorRepository = $this->em->getRepository(Color::class);
    }

    /**
     * @required
     */
    public function setRelationMapper(RelationMapper $relationMapper): LdrawLoader
    {
        $this->relationMapper = $relationMapper;

        return $this;
    }

    /**
     * @param string $ldrawLibrary
     */
    public function setLDrawFilesystem($ldrawLibrary)
    {
        if (!file_exists($ldrawLibrary)) {
            $this->logger->error('Wrong library context');
            throw new MissingContextException($ldrawLibrary);
        }

        $adapter = new Local($ldrawLibrary);
        $this->ldrawFilesystem = new Filesystem($adapter);
        $this->stlConverter->setLDrawFilesystem($this->ldrawFilesystem);
    }

    /**
     * Download library form $url, unzip archive and return directory path with library.
     *
     * @param $url
     *
     * @return bool|string
     */
    public function downloadLibrary($url)
    {
        $this->output->writeln([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            '<fg=cyan>Downloading LDraw library</>',
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        try {
            $libraryZip = $this->downloadFile($url);

            $temp_dir = tempnam(sys_get_temp_dir(), 'printabrick.');
            if (file_exists($temp_dir)) {
                unlink($temp_dir);
            }
            mkdir($temp_dir);

            $zip = new \ZipArchive();
            $zip->open($libraryZip);
            $zip->extractTo($temp_dir);
            $zip->close();
            unlink($libraryZip);

            $this->output->writeln(['<info>LDraw libary downloaded</info>']);

            // return ldraw directory if in zip file
            if (file_exists($temp_dir.'/ldraw/')) {
                return $temp_dir.'/ldraw/';
            }
        } catch (\Exception $exception) {
            throw new \LogicException('Falied to open Zip archive');
        }

        return $temp_dir;
    }

    /**
     * Load one model into database.
     *
     * @param string $filepath Absolute filepath of model to load
     */
    public function loadOne($filepath)
    {
        if (!$this->ldrawFilesystem) {
            throw new MissingContextException('LDrawLibrary');
        }

        $this->em->transactional(function () use ($filepath) {
            // Setup local file context
            $this->fileContext = new Filesystem(new Local(dirname($filepath)));

            $model = $this->loadModel(basename($filepath));

            $this->em->flush();
        });
    }

    /**
     * Load all models from ldraw library context into database.
     */
    public function loadAll()
    {
        ini_set('memory_limit', '2G');

        if (!$this->ldrawFilesystem) {
            throw new MissingContextException('LDrawLibrary');
        }

        $this->output->writeln([
            '<fg=cyan>------------------------------------------------------------------------------</>',
            "<fg=cyan>Loading models from LDraw library:</> <comment>{$this->ldrawFilesystem->getAdapter()->getPathPrefix()}</comment>",
            '<fg=cyan>------------------------------------------------------------------------------</>',
        ]);

        $files = $this->ldrawFilesystem->listContents('parts', false);

        $this->output->progressStart(count($files));

        foreach ($files as $index => $file) {
            if ('file' === $file['type'] && 'dat' === $file['extension']) {
                $this->em->transactional(
                    function () use ($file, $index) {
                        try {
                            $this->loadModel($file['path']);
                        } catch (\Exception $e) {
                            $this->logger->error($e->getMessage(), [$e]);
                        }

                        // clear managed objects to avoid memory issues
                        if (0 === $index % 300) {
                            $this->em->flush();
                        }
                    }
                );
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    /**
     * Load model entity and all related submodels into database while generating stl file of model.
     * Uses LDView to convert LDraw .dat to .stl.
     */
    public function loadModel(string $filePath, array $parents = [])
    {
        // Return model from database if rewrite is not enabled
        if ($model = $this->modelRepository->find(basename($filePath, '.dat'))) {
            if ($this->rewrite && empty($parents)) {
//                $this->em->remove($model);
//                $this->em->flush();
            } else {
                return $model;
            }
        }

        $modelId = basename($filePath, '.dat');
        if (in_array($modelId, $parents, true)) {
            return null;
        }

        try {
            $data = $this->readModelFile($filePath, $absoluteFilePath);
            $parsedArray = $this->ldModelParser->parse($data);
        } catch (FileNotFoundException $e) {
            $this->logger->error($e->getMessage(), [$e]);

            return null;
        } catch (ParseErrorException $e) {
            $this->logger->error($e->getMessage(), [$e]);

            return null;
        }

        // Check if model fulfills rules and should be loaded
        if ($this->isModelIncluded($parsedArray)) {
            $model = $this->modelRepository->getOrCreate($parsedArray['id']);

            $aliasOf = $this->getParentId($parsedArray['id']);

            if ($parentId = ($aliasOf ?? $parsedArray['parent'])) {
                // Recursively load model parent (if any) and add model id as alias of parent
                $parents[] = $parsedArray['id'];

                try {
                    if (($parentFilePath = $this->getModelFilePath($parentId)) && ($parentModel = $this->loadModel($parentFilePath, $parents))) {
                        $alias = $this->aliasRepository->getOrCreate($model, $parentModel);
                        $alias->setAliasType($aliasOf ? 'A' : $parsedArray['type'][0]);

                        $parentModel->addChild($alias);
                        $model->addParent($alias);
                    }
                } catch (FileNotFoundException $exception) {
                    $this->logger->error("Parent model file {$parentId}.dat not found", ['model' => $modelId]);
                }
            } else {
                // Recursively load models of sub-parts
                foreach ($parsedArray['sub_parts'] as $subpartId => $colors) {
                    foreach ($colors as $color => $count) {
                        // Try to find model of subpart
                        try {
                            if (($subpartFilePath = $this->getModelFilePath(
                                    $subpartId
                                )) && ($subModel = $this->loadModel($subpartFilePath))) {
                                $subpart = $this->subpartRepository->getOrCreate(
                                    $model,
                                    $subModel,
                                    $count,
                                    $this->colorRepository->getOrCreate(16 === $color ? -1 : $color)
                                );
                                $model->addChild($subpart);
                            }
                        } catch (FileNotFoundException $exception) {
                            $this->logger->error("Sub-part model file {$subpartId}.dat not found", ['model' => $modelId]);
                        }
                    }
                }
            }

            // Add Keywords to model
            foreach ($parsedArray['keywords'] as $keyword) {
                $keyword = $this->keywordRepository->getOrCreate(stripslashes(strtolower(trim($keyword))));
                $model->addKeyword($keyword);
            }

            try {
                if (in_array($parsedArray['type'], [ModelType::PART, ModelType::SHORTCUT], true)) {
                    $stl = $this->stlConverter->datToStl(
                        $absoluteFilePath,
                        $parsedArray['modified']
                    );
                }

                $model
                    ->setPath($stl ?? null)
                    ->setName($parsedArray['name'])
                    ->setCategory($this->categoryRepository->getOrCreate($parsedArray['category']))
                    ->setAuthor($this->authorRepository->getOrCreate($parsedArray['author']))
                    ->setType($this->modelTypeRepository->getOrCreate($parsedArray['type']))
                    ->setModified($parsedArray['modified']);

                $this->em->persist($model);
            } catch (ConversionFailedException $e) {
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
     * @return string
     */
    private function getParentId($modelId): ?string
    {
        if ($this->relationMapper && $this->relationMapper->find($modelId, 'alias_model') !== $modelId) {
            return $this->relationMapper->find($modelId, 'alias_model');
        }

        return null;
    }

    private function getModelFilePath($id, ?string &$absoluteFilePath = null)
    {
        // Replace "\" directory separator used inside ldraw model files with system directory separator
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($id).'.dat');

        // Try to find model in current file's directory
        if ($this->fileContext && $this->fileContext->has($filename)) {
            $absoluteFilePath = $this->fileContext->getAdapter()->getPathPrefix().$filename;

            return $filename;
        }

        if ($this->ldrawFilesystem) {
            // Try to find model in current LDRAW\PARTS sub-directory
            $path = 'parts'.DIRECTORY_SEPARATOR.$filename;
            if ($this->ldrawFilesystem->has('parts'.DIRECTORY_SEPARATOR.$filename)) {
                $absoluteFilePath = $this->ldrawFilesystem->getAdapter()->getPathPrefix().$path;

                return 'parts'.DIRECTORY_SEPARATOR.$filename;
            }
            // Try to find model in current LDRAW\P sub-directory
            if ($this->ldrawFilesystem->has('p'.DIRECTORY_SEPARATOR.$filename)) {
                $absoluteFilePath = $this->ldrawFilesystem->getAdapter()->getPathPrefix().$path;

                return 'p'.DIRECTORY_SEPARATOR.$filename;
            }
        }

        throw new FileNotFoundException($id);
    }

    /**
     *  Find submodel file inside model file context or inside LDraw library context.
     *
     *  LDraw.org Standards: File Format 1.0.2 (http://www.ldraw.org/article/218.html)
     *
     *  "Sub-files can be located in the LDRAW\PARTS sub-directory, the LDRAW\P sub-directory, the LDRAW\MODELS sub-directory,
     *  the current file's directory, a path relative to one of these directories, or a full path may be specified. Sub-parts are typically
     *  stored in the LDRAW\PARTS\S sub-directory and so are referenced as s\subpart.dat, while hi-res primitives are stored in the
     *  LDRAW\P\48 sub-directory and so referenced as 48\hires.dat"
     *
     * @param string $id ldraw model id
     *
     * @throws FileNotFoundException
     */
    private function readModelFile(string $pathOrId, ?string &$absoluteFilePath = null): string
    {
        // Replace "\" directory separator used inside ldraw model files with system directory separator
        $filepath = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($pathOrId));
        if (!str_ends_with($filepath, '.dat')) {
            $filepath .= '.dat';
        }

        // Try to find model in current file's directory
        if ($this->fileContext && $this->fileContext->has($filepath)) {
            $absoluteFilePath = $this->fileContext->getAdapter()->getPathPrefix().$filepath;

            return $this->fileContext->read($filepath);
        }

        if ($this->ldrawFilesystem) {
            if ($this->ldrawFilesystem->has($filepath)) {
                $absoluteFilePath = $this->ldrawFilesystem->getAdapter()->getPathPrefix().$filepath;

                return $this->ldrawFilesystem->read($filepath);
            }

            // Try to find model in current LDRAW\PARTS sub-directory
            $path = 'parts'.DIRECTORY_SEPARATOR.$filepath;
            if ($this->ldrawFilesystem->has($path)) {
                $absoluteFilePath = $this->ldrawFilesystem->getAdapter()->getPathPrefix().$path;

                return $this->ldrawFilesystem->read($path);
            }

            // Try to find model in current LDRAW\P sub-directory
            $path = 'p'.DIRECTORY_SEPARATOR.$filepath;
            if ($this->ldrawFilesystem->has($path)) {
                $absoluteFilePath = $this->ldrawFilesystem->getAdapter()->getPathPrefix().$path;

                return $this->ldrawFilesystem->read($path);
            }
        }

        throw new FileNotFoundException($filepath);
    }

    /**
     * Determine if model file should be loaded into database.
     *
     * @param $modelArray
     */
    private function isModelIncluded($modelArray): bool
    {
        // Do not include part primitives and sub-parts
        if (in_array($modelArray['type'], ['48_Primitive', '8_Primitive', 'Primitive', 'Subpart'])) {
            $this->logger->info('Model skipped.', ['number' => $modelArray['id'], 'type' => $modelArray['type']]);

            return false;
        }
        // Do not include Pov-RAY file
        if ('Pov-RAY' === $modelArray['category']) {
            $this->logger->info('Model skipped.', ['number' => $modelArray['id'], 'category' => $modelArray['category']]);

            return false;
        }
        // Do not include sticker models
        if ('Sticker' === $modelArray['type']) {
            $this->logger->info('Model skipped.', ['number' => $modelArray['id'], 'type' => $modelArray['type']]);

            return false;
        }
        // Do not include models without permission to redistribute
        if ('Redistributable under CCAL version 2.0' !== $modelArray['license']) {
            $this->logger->info('Model skipped.', ['number' => $modelArray['id'], 'license' => $modelArray['license']]);

            return false;
        }

        return true;
    }
}
