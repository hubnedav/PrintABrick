<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Repository\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Get existing entity or create new.
     *
     * @param $name
     *
     * @return Category
     */
    public function getOrCreate($name)
    {
        if (($category = $this->findByName($name)) == null) {
            $category = new Category();
            $category->setName($name);
        }

        return $category;
    }
}
