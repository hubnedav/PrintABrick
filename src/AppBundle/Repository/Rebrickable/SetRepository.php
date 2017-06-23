<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class SetRepository extends BaseRepository
{
    public function findAllByPart(Part $part)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory.set = s.id')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'inventory.id = inventory_part.inventory')
            ->join(Part::class, 'part', Join::WITH, 'inventory_part.part = part.id')
            ->where('part.id LIKE :number')
            ->setParameter('number', $part->getId())
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllByModel(Model $model)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory.set = s.id')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'inventory.id = inventory_part.inventory')
            ->join(Part::class, 'part', Join::WITH, 'inventory_part.part = part.id')
            ->where('part.model = :model')
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

    public function count()
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->select('count(s)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function getMaxYear()
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('MAX(s.year)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
