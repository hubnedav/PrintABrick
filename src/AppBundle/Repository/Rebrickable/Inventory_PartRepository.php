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

        $version = $this->getEntityManager()->getRepository(Inventory::class)->createQueryBuilder('inventory')
            ->select('MAX(inventory.version)')
            ->groupBy('inventory.set')
            ->where('inventory.set = :setNumber')
            ->setParameter('setNumber',$number)
            ->getQuery()->getResult();

        $queryBuilder
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory.id')
            ->where('inventory.set = :number')
            ->setParameter('number', $number)
            ->andWhere('inventory_part.spare = FALSE')
            ->andWhere('inventory.version = :version')
            ->setParameter('version',$version)
            ->distinct(true);



        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllSpareBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('inventory_part');

        $version = $this->getEntityManager()->getRepository(Inventory::class)->createQueryBuilder('inventory')
            ->select('MAX(inventory.version)')
            ->groupBy('inventory.set')
            ->where('inventory.set = :setNumber')
            ->setParameter('setNumber',$number)
            ->getQuery()->getResult();


        $queryBuilder
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory.id')
            ->where('inventory.set = :number')
            ->setParameter('number', $number)
            ->andWhere('inventory_part.spare = TRUE')
            ->andWhere('inventory.version = 1')
            ->andWhere('inventory.version = :version')
            ->setParameter('version',$version)
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
