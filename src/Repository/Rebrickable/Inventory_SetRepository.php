<?php

namespace App\Repository\Rebrickable;

use App\Entity\Rebrickable\Inventory;
use App\Entity\Rebrickable\Inventory_Set;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Inventory_SetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory_Set::class);
    }

    public function findAllBySetNumber($number)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        if ($inventory) {
            $queryBuilder = $this->createQueryBuilder('inventory_set')
                ->where('inventory_set.inventory = :inventory')
                ->setParameter('inventory', $inventory);

            return $queryBuilder->getQuery()->getResult();
        }

        return null;
    }
}
