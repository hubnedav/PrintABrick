<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\BaseRepository;

class InventoryRepository extends BaseRepository
{
    public function findNewestInventoryBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('inventory')
            ->where('inventory.set = :setNumber')
            ->setParameter('setNumber', $number)
            ->orderBy('inventory.version', 'DESC')
            ->setMaxResults(1)
            ->select('inventory.id');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
