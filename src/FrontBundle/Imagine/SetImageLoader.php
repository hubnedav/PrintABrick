<?php

namespace FrontBundle\Imagine;

use AppBundle\Api\Manager\BricksetManager;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;

class SetImageLoader extends BaseImageLoader
{
    /** @var BricksetManager */
    private $bricksetManager;

    private $rebrickableContext = 'http://rebrickable.com/media/sets/';

    /** @var Filesystem */
    private $mediaFilesystem;

    /**
     * SetImageLoader constructor.
     *
     * @param BricksetManager     $bricksetManager
     * @param FilesystemInterface $mediaFilesystem
     */
    public function __construct(BricksetManager $bricksetManager, FilesystemInterface $mediaFilesystem)
    {
        $this->bricksetManager = $bricksetManager;
        $this->mediaFilesystem = $mediaFilesystem;
    }

    public function find($path)
    {
        // try to load image from rebrickable website
        if ($this->remoteFileExists($this->rebrickableContext.strtolower($path))) {
            $context = stream_context_create(['http' => ['header' => 'Connection: close\r\n']]);

            return file_get_contents($this->rebrickableContext.strtolower($path), false, $context);
        }

        // Load part entity form brickset api and get image path from response
        try {
            if (preg_match('/^(.*)(.png|.jpg)$/', $path, $match)) {
                $set = $this->bricksetManager->getSetByNumber($match[1]);

                if ($set && $set->getImage()) {
                    return file_get_contents($set->getImageURL());
                }
            }
        } catch (\Exception $e) {
            throw new NotLoadableException(sprintf('Source image %s could not be loaded.', $path), $e->getCode(), $e);
        }

        throw new NotLoadableException(sprintf('Source image %s not found.', $path));
    }
}
