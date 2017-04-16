<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class Inventory_PartRepository extends BaseRepository
{
    public function findAllRegularBySetNumber($number)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder = $this->createQueryBuilder('inventory_part')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory')
            ->join(Part::class, 'part', JOIN::WITH, 'inventory_part.part = part.number')
            ->where('part.category != 17')
            ->andWhere('inventory.id = :inventoryId')
            ->setParameter('inventoryId', $inventory->getId())
            ->andWhere('inventory_part.spare = FALSE')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllSpareBySetNumber($number)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder = $this->createQueryBuilder('inventory_part')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory')
            ->join(Part::class, 'part', JOIN::WITH, 'inventory_part.part = part.number')
            ->where('part.category != 17')
            ->andWhere('inventory.id = :inventoryId')
            ->setParameter('inventoryId', $inventory->getId())
            ->andWhere('inventory_part.spare = TRUE')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
