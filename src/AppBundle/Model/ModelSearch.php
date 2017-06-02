<?php

namespace AppBundle\Model;

use AppBundle\Entity\LDraw\Category;

class ModelSearch
{
    /** @var string */
    protected $query;

    /** @var Category */
    protected $category;

    /**
     * ModelSearch constructor.
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
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}
