<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class Inventory_PartRepository extends BaseRepository
{
    public function findAllBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('inventory_part');

        $queryBuilder
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory.id')
            ->join(Set::class, 's', Join::WITH, 'inventory.set = s.number')
            ->where('s.number LIKE :number')
            ->setParameter('number', $number)
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
