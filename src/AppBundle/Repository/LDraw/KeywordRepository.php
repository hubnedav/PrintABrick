<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Repository\BaseRepository;

class KeywordRepository extends BaseRepository
{
    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }
}
