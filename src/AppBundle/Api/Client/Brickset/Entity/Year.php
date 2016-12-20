<?php

namespace AppBundle\Api\Client\Brickset\Entity;

class Year
{
    protected $theme;
    protected $year;
    protected $setCount;

    /**
     * Year constructor.
     */
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
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
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
}
