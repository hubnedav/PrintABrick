<?php

namespace AppBundle\Api\Client\Rebrickable\Entity;

class Part
{
    /**
     * Part ID number.
     *
     * @var int
     */
    private $number;
    /**
     * Part Name.
     *
     * @var string
     */
    private $name;
    /**
     * Part category id.
     *
     * @var int
     */
    private $categoryId;

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
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

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
