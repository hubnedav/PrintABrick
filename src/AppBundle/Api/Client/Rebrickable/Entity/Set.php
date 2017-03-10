<?php

namespace AppBundle\Api\Client\Rebrickable\Entity;

class Set
{
    /**
     * Set ID if relevan to type.
     *
     * @var int
     */
    private $number;
    /**
     * Set description.
     *
     * @var string
     */
    private $name;
    /**
     * Year of set release.
     *
     * @var int
     */
    private $year;
    /**
     * Count of parts in set.
     *
     * @var int
     */
    private $numParts;
    /**
     * Rebrickable internal theme id.
     *
     * @var int
     */
    private $themeId;
    /**
     * Rebrickable URL of the main set image.
     *
     * @var string
     */
    private $imgUrl;
    /**
     * Rebrickable URL to the Set Details page.
     *
     * @var string
     */
    private $url;

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return int
     */
    public function getNumParts()
    {
        return $this->numParts;
    }

    /**
     * @param int $numParts
     */
    public function setNumParts($numParts)
    {
        $this->numParts = $numParts;
    }

    /**
     * @return int
     */
    public function getThemeId()
    {
        return $this->themeId;
    }

    /**
     * @param int $themeId
     */
    public function setThemeId($themeId)
    {
        $this->themeId = $themeId;
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
}
