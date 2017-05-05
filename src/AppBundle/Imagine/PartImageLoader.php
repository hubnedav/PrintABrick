<?php

namespace AppBundle\Imagine;

use AppBundle\Api\Manager\RebrickableManager;
use League\Flysystem\Filesystem;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;

class PartImageLoader implements LoaderInterface
{
    /** @var Filesystem */
    private $mediaFilesystem;

    /** @var RebrickableManager */
    private $rebrickableManager;

    private $rebrickableContext = 'http://rebrickable.com/media/parts/ldraw/';

    /**
     * LocalStreamLoader constructor.
     *
     * @param $rebrickableManager
     * @param $mediaFilesystem
     */
    public function __construct($rebrickableManager, $mediaFilesystem)
    {
        $this->mediaFilesystem = $mediaFilesystem;
        $this->rebrickableManager = $rebrickableManager;
    }

    public function find($path)
    {
        // try to load image from local mediaFilesystem
        if ($this->mediaFilesystem->has('/images/'.$path)) {
            return $this->mediaFilesystem->read('/images/'.$path);
        }

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
            if (preg_match('/^(.*)\/(.*).png$/', $path, $match)) {
                $part = $this->rebrickableManager->getPart($match[2]);

                if ($part && $part->getImgUrl()) {
                    return file_get_contents($part->getImgUrl());
                }
            }
        } catch (\Exception $e) {
            throw new NotLoadableException(sprintf('Source image %s could not be loaded.', $path), $e->getCode(), $e);
        }

        return $this->mediaFilesystem->read('noimage.png');
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function remoteFileExists($url)
    {
        $resource = curl_init($url);
        curl_setopt($resource, CURLOPT_NOBODY, true);
        curl_exec($resource);
        $status = curl_getinfo($resource, CURLINFO_HTTP_CODE);
        curl_close($resource);

        return $status === 200 ? true : false;
    }
}
