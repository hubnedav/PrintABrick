<?php

namespace AppBundle\Api\Client\Brickset\Entity;

class AdditionalImage
{
    /**
     * @var string
     */
    protected $thumbnailURL = null;

    /**
     * @var string
     */
    protected $largeThumbnailURL = null;

    /**
     * @var string
     */
    protected $imageURL = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getThumbnailURL()
    {
        return $this->thumbnailURL;
    }

    /**
     * @param string $thumbnailURL
     *
     * @return AdditionalImage
     */
    public function setThumbnailURL($thumbnailURL)
    {
        $this->thumbnailURL = $thumbnailURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getLargeThumbnailURL()
    {
        return $this->largeThumbnailURL;
    }

    /**
     * @param string $largeThumbnailURL
     *
     * @return AdditionalImage
     */
    public function setLargeThumbnailURL($largeThumbnailURL)
    {
        $this->largeThumbnailURL = $largeThumbnailURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageURL()
    {
        return $this->imageURL;
    }

    /**
     * @param string $imageURL
     *
     * @return AdditionalImage
     */
    public function setImageURL($imageURL)
    {
        $this->imageURL = $imageURL;

        return $this;
    }
}
