<?php

namespace AppBundle\Service\Loader;


use AppBundle\Entity\LDraw\Model;
use AppBundle\Service\StlRendererService;
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

    public function loadColorFromRebrickable($color, $path = null)
    {
        if(!$path) {
            $path = $this->rebrickableDownloadUrl."ldraw/parts_{$color}.zip";
        }

        $file = $this->downloadFile($path);
        $zip = new \ZipArchive($file);

        if ($zip->open($file) === TRUE) {
            $this->output->writeln([
                "Extracting ZIP file into {$this->mediaFilesystem->getAdapter()->getPathPrefix()}images/{$color}"
            ]);
            $zip->extractTo($this->mediaFilesystem->getAdapter()->getPathPrefix().'images'.DIRECTORY_SEPARATOR.$color);
            $zip->close();
            $this->output->writeln(['Done!']);
        } else {
            $this->output->writeln(['<error>Extraction of file failed!</error>']);
        }
    }

    public function loadMissingModelImages($color) {
        $models = $this->em->getRepository(Model::class)->findAll();

        $this->initProgressBar(count($models));
        foreach ($models as $model) {
            $this->progressBar->setMessage($model->getNumber());
            if(!$this->mediaFilesystem->has('images'.DIRECTORY_SEPARATOR.$color.DIRECTORY_SEPARATOR.$model->getNumber().'.png')) {
                try {
                    $this->stlRendererService->render(
                        $this->mediaFilesystem->getAdapter()->getPathPrefix().$model->getPath(),
                        $this->mediaFilesystem->getAdapter()->getPathPrefix().'images'.DIRECTORY_SEPARATOR.$color.DIRECTORY_SEPARATOR
                    );
                } catch (\Exception $e) {
                    dump($e->getMessage());
                }
            }
            $this->progressBar->advance();
        }
    }
}