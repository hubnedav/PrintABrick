<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Category;
use App\Repository\BaseRepository;

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
        if (null == ($category = $this->findByName($name))) {
            $category = new Category();
            $category->setName($name);
        }

        return $category;
    }
}
