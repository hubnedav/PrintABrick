<?php

namespace AppBundle\Model;

use AppBundle\Entity\Rebrickable\Theme;

class SetSearch
{
    /** @var string */
    protected $query;

    /** @var NumberRange */
    protected $year;

    /** @var NumberRange */
    protected $partCount;

    /** @var Theme */
    protected $theme;

    /**
     * SetSearch constructor.
     *
     * @param string $query
     */
    public function __construct($query = '')
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return NumberRange
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param NumberRange $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @return NumberRange
     */
    public function getPartCount()
    {
        return $this->partCount;
    }

    /**
     * @param NumberRange $partCount
     */
    public function setPartCount($partCount)
    {
        $this->partCount = $partCount;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }
}
