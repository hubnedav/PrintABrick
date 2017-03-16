<?php

namespace AppBundle\Api\Client\Rebrickable\Entity;

class Part extends \AppBundle\Entity\Rebrickable\Part
{
    /**
     * Year the part first appeared in sets.
     *
     * @var int
     */
    private $yearFrom;
    /**
     * Year the part was last seen in sets.
     *
     * @var int
     */
    private $yearTo;
    /**
     * Array of colors the part appears in.
     *
     * @var array
     */
    private $externalIds;
    /**
     * Array of molds of part.
     *
     * @var array
     */
    private $molds;
    /**
     * Array of prints the part appears in.
     *
     * @var array
     */
    private $prints;
    /**
     * Array of alternative prts.
     *
     * @var array
     */
    private $alternates;
    /**
     * Rebrickable URL to the Part Details page.
     *
     * @var string
     */
    private $url;
    /**
     * Rebrickable URL of the main part image (tries to use most common color).
     *
     * @var string
     */
    private $imgUrl;

    /**
     * @return int
     */
    public function getYearFrom()
    {
        return $this->yearFrom;
    }

    /**
     * @param int $yearFrom
     */
    public function setYearFrom($yearFrom)
    {
        $this->yearFrom = $yearFrom;
    }

    /**
     * @return int
     */
    public function getYearTo()
    {
        return $this->yearTo;
    }

    /**
     * @param int $yearTo
     */
    public function setYearTo($yearTo)
    {
        $this->yearTo = $yearTo;
    }

    /**
     * @return array
     */
    public function getExternalIds()
    {
        return $this->externalIds;
    }

    /**
     * @param array $externalIds
     */
    public function setExternalIds($externalIds)
    {
        $this->externalIds = $externalIds;
    }

    /**
     * @return array
     */
    public function getAlternates()
    {
        return $this->alternates;
    }

    /**
     * @param array $alternates
     */
    public function setAlternates($alternates)
    {
        $this->alternates = $alternates;
    }

    /**
     * @return array
     */
    public function getMolds()
    {
        return $this->molds;
    }

    /**
     * @param array $molds
     */
    public function setMolds($molds)
    {
        $this->molds = $molds;
    }

    /**
     * @return array
     */
    public function getPrints()
    {
        return $this->prints;
    }

    /**
     * @param array $prints
     */
    public function setPrints($prints)
    {
        $this->prints = $prints;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * @param string $imgUrl
     */
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;
    }
}
