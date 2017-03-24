<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Repository\BaseRepository;

class Part_RelationRepository extends BaseRepository
{
    public function findByForeignKeys($parent, $child, $relationType)
    {
        return $this->find(['parent' => $parent, 'child' => $child, 'type' => $relationType]);
    }
}
