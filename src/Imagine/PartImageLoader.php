<?php

namespace App\Imagine;

use App\Api\Manager\RebrickableManager;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;

class PartImageLoader extends BaseImageLoader
{
    private FilesystemInterface $mediaFilesystem;
    private RebrickableManager $rebrickableManager;
    private string $rebrickableContext = 'https://cdn.rebrickable.com/media/parts/ldraw/';

    /**
     * PartImageLoader constructor.
     */
    public function __construct(RebrickableManager $rebrickableManager, FilesystemInterface $mediaFilesystem)
    {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->rebrickableManager = $rebrickableManager;
    }

    public function find($path)
    {
        if (preg_match('/^(.*)\/(.*)(.png|.jpg)$/', $path, $match)) {
            $color = $match[1];
            $number = $match[2];

            // try to load image from local mediaFilesystem
            if ($this->mediaFilesystem->has('/images/'.$path)) {
                return $this->mediaFilesystem->read('/images/'.$path);
            }

            // try to load image from rebrickable website
            if ($this->remoteFileExists($this->rebrickableContext.'/'.$color.'/'.$number.'.png')) {
                $context = stream_context_create(['http' => ['header' => 'Connection: close\r\n']]);

                return file_get_contents($this->rebrickableContext.'/'.$color.'/'.$number.'.png', false, $context);
            }

            // Load part entity form rebrickable api and get image path from response
            try {
                $part = $this->rebrickableManager->getPart($number);

                if ($part && $part->getImgUrl()) {
                    return file_get_contents($part->getImgUrl());
                }
            } catch (\Exception $e) {
                throw new NotLoadableException(sprintf('Source image %s could not be loaded.', $path), $e->getCode(), $e);
            }
        }

        throw new NotLoadableException(sprintf('Source image %s not found.', $path));
    }
}
