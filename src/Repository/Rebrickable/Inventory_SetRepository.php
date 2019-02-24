<?php

namespace App\Repository\Rebrickable;

use App\Entity\Rebrickable\Inventory;
use App\Repository\BaseRepository;

class Inventory_SetRepository extends BaseRepository
{
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
