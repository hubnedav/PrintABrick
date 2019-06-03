<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Keyword;
use App\Repository\BaseRepository;

class KeywordRepository extends BaseRepository
{
    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Create new Keyword entity with $name or retrieve one.
     *
     * @param $name
     *
     * @return Keyword
     */
    public function getOrCreate($name)
    {
        if (null == ($keyword = $this->findByName($name))) {
            $keyword = new Keyword();
            $keyword->setName($name);
        }

        return $keyword;
    }
}
