<?php

namespace AppBundle\Client\Brickset\Entity;

class Year
{
    protected $theme;
    protected $year;
    protected $setCount;

    /**
     * Year constructor.
     *
     * @param $theme
     * @param $year
     * @param $setCount
     */
    public function __construct($theme, $year, $setCount)
    {
        $this->theme = $theme;
        $this->year = $year;
        $this->setCount = $setCount;
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
