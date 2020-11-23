<?php

namespace App\Repository\Rebrickable;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Inventory;
use App\Entity\Rebrickable\Inventory_Part;
use App\Entity\Rebrickable\Part;
use App\Entity\Rebrickable\Set;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class SetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Set::class);
    }

    public function findAllByPart(Part $part)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory.set = s.id')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'inventory.id = inventory_part.inventory')
            ->join(Part::class, 'part', Join::WITH, 'inventory_part.part = part.id')
            ->andWhere('part.id LIKE :number')
            ->setParameter('number', $part->getId())
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllByModel(Model $model)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->leftJoin(Inventory::class, 'inventory', JOIN::WITH, 'inventory.set = s.id')
            ->leftJoin(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'inventory.id = inventory_part.inventory')
            ->leftJoin('inventory_part.part', 'part')
            ->leftJoin('part.model', 'model')
            ->andWhere('model.id = :model')
            ->setParameter('model', $model->getId())
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function getMinPartCount()
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('MIN(s.partCount)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getMaxPartCount()
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('MAX(s.partCount)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getMinYear()
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('MIN(s.year)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getMaxYear()
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('MAX(s.year)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
