<?php

namespace LoaderBundle\Service;

use AppBundle\Entity\LDraw\Model;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use LoaderBundle\Exception\FileException;
use LoaderBundle\Service\Stl\StlRendererService;
use Psr\Log\LoggerInterface;

class ImageLoader extends BaseLoader
{
    /** @var FilesystemInterface */
    private $mediaFilesystem;

    /** @var string */
    private $rebrickableDownloadUrl;

    /** @var StlRendererService */
    private $stlRendererService;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, FilesystemInterface $mediaFilesystem, $rebrickableDownloadUrl, StlRendererService $stlRendererService)
    {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->rebrickableDownloadUrl = $rebrickableDownloadUrl;
        $this->stlRendererService = $stlRendererService;

        parent::__construct($em, $logger);
    }

    /**
     * Download ZIP file with part images from rebrickable and unzip file to filesystem.
     *
     * @param int $color color id used by rebrickable
     *
     * @throws FileException
     */
    public function loadColorFromRebrickable($color)
    {
        $path = $this->rebrickableDownloadUrl."ldraw/parts_{$color}.zip";

        $file = $this->downloadFile($path);
        $zip = new \ZipArchive();

        if ($zip->open($file) === true) {
            $this->writeOutput([
                "Extracting ZIP file into {$this->mediaFilesystem->getAdapter()->getPathPrefix()}images/{$color}",
            ]);
            $zip->extractTo($this->mediaFilesystem->getAdapter()->getPathPrefix().'images'.DIRECTORY_SEPARATOR.$color);
            $zip->close();
            $this->writeOutput(['Done!']);
        } else {
            $this->logger->error('Extraction of file failed!');
            throw new FileException($file);
        }
    }

    /**
     * Load missing images of models.
     */
    public function loadMissingModelImages()
    {
        // Get models without image
        $missing = [];
        $models = $this->em->getRepository(Model::class)->findAll();
        /** @var Model $model */
        foreach ($models as $model) {
            if (!$this->mediaFilesystem->has('images'.DIRECTORY_SEPARATOR.'-1'.DIRECTORY_SEPARATOR.$model->getId().'.png')) {
                $missing[] = $model;
            }
        }
        unset($models);

        // Render images
        $this->writeOutput([
            'Rendering missing images of models',
        ]);
        $this->initProgressBar(count($missing));
        foreach ($missing as $model) {
            $this->progressBar->setMessage($model->getId());

            try {
                $this->loadModelImage($this->mediaFilesystem->getAdapter()->getPathPrefix().$model->getPath());
            } catch (\Exception $e) {
                $this->logger->error('Error rendering model '.$model->getId().' image', [$e->getMessage()]);
            }
            $this->progressBar->advance();
        }

        $this->progressBar->finish();
    }

    /**
     * Render model and save image into.
     *
     * @param $file
     */
    public function loadModelImage($file)
    {
        $this->stlRendererService->render(
            $file,
            $this->mediaFilesystem->getAdapter()->getPathPrefix().'images'.DIRECTORY_SEPARATOR.'-1'.DIRECTORY_SEPARATOR
        );
    }
}
