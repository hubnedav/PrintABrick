<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Repository\BaseRepository;

class SubpartRepository extends BaseRepository
{
    public function findOneByKeys($parent, $child)
    {
        return $this->find(['parent' => $parent, 'subpart' => $child]);
    }
}
