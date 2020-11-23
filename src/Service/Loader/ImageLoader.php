<?php

namespace App\Service\Loader;

use App\Entity\LDraw\Model;
use App\Exception\FileException;
use App\Service\Stl\StlRenderer;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;

class ImageLoader extends LoggerAwareLoader
{
    private EntityManagerInterface $em;

    private FilesystemInterface $mediaFilesystem;

    private string $rebrickableCDN;

    private StlRenderer $stlRendererService;

    public function __construct(EntityManagerInterface $em, FilesystemInterface $mediaFilesystem, $rebrickableDownloadsCdn, StlRenderer $stlRendererService)
    {
        parent::__construct();
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger();
        $this->mediaFilesystem = $mediaFilesystem;
        $this->rebrickableCDN = $rebrickableDownloadsCdn;
        $this->stlRendererService = $stlRendererService;
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
        $path = $this->rebrickableCDN."ldraw/parts_{$color}.zip";

        $file = $this->downloadFile($path);
        $zip = new \ZipArchive();

        if (true === $zip->open($file)) {
            $this->output->writeln([
                "Extracting ZIP file into {$this->mediaFilesystem->getAdapter()->getPathPrefix()}images/{$color}",
            ]);
            $zip->extractTo($this->mediaFilesystem->getAdapter()->getPathPrefix().'images'.DIRECTORY_SEPARATOR.$color);
            $zip->close();
            $this->output->writeln(['Done!']);
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
        $this->output->writeln([
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
