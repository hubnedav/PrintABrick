<?php

namespace AppBundle\Api\Client\Brickset\Entity;

class Subtheme
{
    /**
     * @var string
     */
    protected $theme;

    /**
     * @var string
     */
    protected $subtheme;

    /**
     * @var
     */
    protected $setCount;
    /**
     * @var int
     */
    protected $yearFrom;
    /**
     * @var int
     */
    protected $yearTo;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    /**
     * @return mixed
     */
    public function getSetCount()
    {
        return $this->setCount;
    }

    /**
     * @param mixed $setCount
     */
    public function setSetCount($setCount)
    {
        $this->setCount = $setCount;
    }

    /**
     * @return string
     */
    public function getSubtheme()
    {
        return $this->subtheme;
    }

    /**
     * @param string $subtheme
     */
    public function setSubtheme($subtheme)
    {
        $this->subtheme = $subtheme;
    }

    /**
     * @return mixed
     */
    public function getYearFrom()
    {
        return $this->yearFrom;
    }

    /**
     * @param mixed $yearFrom
     */
    public function setYearFrom($yearFrom)
    {
        $this->yearFrom = $yearFrom;
    }

    /**
     * @return mixed
     */
    public function getYearTo()
    {
        return $this->yearTo;
    }

    /**
     * @param mixed $yearTo
     */
    public function setYearTo($yearTo)
    {
        $this->yearTo = $yearTo;
    }
}
