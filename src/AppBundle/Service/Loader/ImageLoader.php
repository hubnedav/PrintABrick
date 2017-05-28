<?php

namespace AppBundle\Service\Loader;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Service\Stl\StlRendererService;
use League\Flysystem\Filesystem;

class ImageLoader extends BaseLoader
{
    /** @var Filesystem */
    private $mediaFilesystem;

    /** @var string */
    private $rebrickableDownloadUrl;

    /** @var StlRendererService */
    private $stlRendererService;

    public function __construct($mediaFilesystem, $rebrickableDownloadUrl, $stlRendererService)
    {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->rebrickableDownloadUrl = $rebrickableDownloadUrl;
        $this->stlRendererService = $stlRendererService;
    }

    /**
     * @param $color
     * @param null $path
     */
    public function loadColorFromRebrickable($color)
    {
        $path = $this->rebrickableDownloadUrl."ldraw/parts_{$color}.zip";

        $file = $this->downloadFile($path);
        $zip = new \ZipArchive($file);

        if ($zip->open($file) === true) {
            $this->output->writeln([
                "Extracting ZIP file into {$this->mediaFilesystem->getAdapter()->getPathPrefix()}images/{$color}",
            ]);
            $zip->extractTo($this->mediaFilesystem->getAdapter()->getPathPrefix().'images'.DIRECTORY_SEPARATOR.$color);
            $zip->close();
            $this->output->writeln(['Done!']);
        } else {
            $this->output->writeln(['<error>Extraction of file failed!</error>']);
        }
    }

    /**
     * Load images of models.
     */
    public function loadMissingModelImages()
    {
        $models = $this->em->getRepository(Model::class)->findAll();

        $this->initProgressBar(count($models));
        foreach ($models as $model) {
            $this->progressBar->setMessage($model->getId());
            if (!$this->mediaFilesystem->has('images'.DIRECTORY_SEPARATOR.'-1'.DIRECTORY_SEPARATOR.$model->getId().'.png')) {
                try {
                    $this->loadModelImage($this->mediaFilesystem->getAdapter()->getPathPrefix().$model->getPath());
                } catch (\Exception $e) {
                    dump($e->getMessage());
                }
            }
            $this->progressBar->advance();
        }
        $this->progressBar->finish();
    }

    /**
     * Render model and save image into co.
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
