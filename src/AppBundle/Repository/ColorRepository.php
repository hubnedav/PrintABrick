<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Set;
use Doctrine\ORM\Query\Expr\Join;

class ColorRepository extends BaseRepository
{
    public function findAllBySet(Set $set)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($set->getNumber());

        $queryBuilder = $this->createQueryBuilder('color');

        $queryBuilder
            ->join(Inventory_Part::class, 'inventory_part', Join::WITH, 'inventory_part.color = color.id')
            ->where('inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory)
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
