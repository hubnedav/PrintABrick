<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\CategoryRepository;

class CategoryManager extends BaseManager
{
    /**
     * CategoryManager constructor.
     *
     * @param CategoryRepository $repository
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Create new Category entity with $name or retrieve one.
     *
     * @param $name
     *
     * @return Category
     */
    public function create($name)
    {
        if (($category = $this->repository->findByName($name)) == null) {
            $category = new Category();
            $category->setName($name);
        }

        return $category;
    }
}
