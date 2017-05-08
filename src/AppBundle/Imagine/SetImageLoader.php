<?php

namespace AppBundle\Imagine;

use AppBundle\Api\Manager\BricksetManager;
use League\Flysystem\Filesystem;
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
     * @param $bricksetManager
     */
    public function __construct($bricksetManager, $mediaFilesystem)
    {
        $this->bricksetManager = $bricksetManager;
        $this->mediaFilesystem = $mediaFilesystem;
    }

    public function find($path)
    {
        // try to load image from rebrickable website
        try {
            if ($this->remoteFileExists($this->rebrickableContext.$path)) {
                return file_get_contents($this->rebrickableContext.$path);
            }
        } catch (\Exception $e) {
            throw new NotLoadableException(sprintf('Source image %s could not be loaded.', $path), $e->getCode(), $e);
        }

        // Load part entity form rebrickable api and get image path from response
        try {
            if (preg_match('/^(.*)[.png|.jpg]$/', $path, $match)) {
                $set = $this->bricksetManager->getSetByNumber($match[1]);

                if ($set && $set->getImage()) {
                    return file_get_contents($set->getImageURL());
                }
            }
        } catch (\Exception $e) {
            throw new NotLoadableException(sprintf('Source image %s could not be loaded.', $path), $e->getCode(), $e);
        }

        return $this->mediaFilesystem->read('noimage.png');
    }
}
