<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Repository\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
