<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Repository\BaseRepository;

class SubpartRepository extends BaseRepository
{
    public function findOneByKeys($parent, $child)
    {
        return $this->find(['parent' => $parent, 'subpart' => $child]);
    }

    /**
     * Create new Subpart relation entity or retrieve one by foreign keys.
     *
     * @param $name
     *
     * @return Subpart
     */
    public function getOrCreate($parent, $child, $count)
    {
        if (($subpart = $this->findOneByKeys($parent, $child))) {
            $subpart->setCount($count);
        } else {
            $subpart = new Subpart();
            $subpart
                ->setParent($parent)
                ->setSubpart($child)
                ->setCount($count);
        }

        return $subpart;
    }
}
