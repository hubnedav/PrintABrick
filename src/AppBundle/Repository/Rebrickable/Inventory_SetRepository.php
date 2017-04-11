<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class Inventory_SetRepository extends BaseRepository
{
    public function findAllBySetNumber($number) {

        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        if($inventory) {
            $queryBuilder = $this->createQueryBuilder('inventory_set')
                ->where('inventory_set.inventory = :inventory')
                ->setParameter('inventory',$inventory->getId());

            return $queryBuilder->getQuery()->getResult();
        }

        return null;
    }
}
