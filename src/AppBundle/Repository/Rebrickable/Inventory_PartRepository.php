<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class Inventory_PartRepository extends BaseRepository
{
    public function findAllRegularBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('inventory_part');

        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory->getId())
            ->andWhere('inventory_part.spare = FALSE')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllSpareBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('inventory_part');

        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory->getId())
            ->andWhere('inventory_part.spare = TRUE')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
