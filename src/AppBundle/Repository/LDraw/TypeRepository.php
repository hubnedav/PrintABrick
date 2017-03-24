<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Repository\BaseRepository;

class TypeRepository extends BaseRepository
{
    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
