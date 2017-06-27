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

    public function find($setNumber)
    {
        $rebrickablePath = $this->rebrickableContext.strtolower($setNumber).'.jpg';

        // try to load image from rebrickable website
        if ($this->remoteFileExists($rebrickablePath)) {
            $context = stream_context_create(['http' => ['header' => 'Connection: close\r\n']]);

            return file_get_contents($rebrickablePath, false, $context);
        }

        // Load part entity form brickset api and get image path from response
        try {
            $set = $this->bricksetManager->getSetByNumber($setNumber);

            if ($set && $set->getImage()) {
                return file_get_contents($set->getImageURL());
            }
        } catch (\Exception $e) {
            throw new NotLoadableException(sprintf('Image %s could not be loaded.', $setNumber), $e->getCode(), $e);
        }

        throw new NotLoadableException(sprintf('Image %s not found.', $setNumber));
    }
}
