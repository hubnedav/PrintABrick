<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Repository\BaseRepository;

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
        if (($keyword = $this->findByName($name)) == null) {
            $keyword = new Keyword();
            $keyword->setName($name);
        }

        return $keyword;
    }
}
