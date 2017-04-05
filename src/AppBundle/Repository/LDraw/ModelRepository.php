<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Type;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class ModelRepository extends BaseRepository
{
    public function findAllByType($type)
    {
        $queryBuilder = $this->createQueryBuilder('model')
            ->join(Type::class, 'type', Join::LEFT_JOIN, 'model.type = :type')
            ->setParameter('type', $type);

        return $queryBuilder->getQuery();
    }

    public function findAllByCategory($category)
    {
        $queryBuilder = $this->createQueryBuilder('model')
            ->join(Type::class, 'type', Join::LEFT_JOIN, 'model.category = :category')
            ->setParameter('category', $category);

        return $queryBuilder->getQuery();
    }

    public function findOneByNumber($number)
    {
        $model = $this->createQueryBuilder('model')
            ->where('model.number LIKE :number')
            ->setParameter('number', $number)
            ->getQuery()->getOneOrNullResult();

        if (!$model) {
            $model = $this->createQueryBuilder('model')
                ->leftJoin(Alias::class, 'alias', JOIN::WITH, 'alias.model = model')
                ->where('alias.number LIKE :number')
                ->setParameter('number', $number)
                ->getQuery()->getOneOrNullResult();
        }

        return $model;
    }

    public function findAllBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('model');

        $queryBuilder
            ->join(Part::class, 'part', JOIN::WITH, 'part.model = model')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'part.number = inventory_part.part')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory.id')
            ->join(Set::class, 's', Join::WITH, 'inventory.set = s.number')
            ->where('s.number LIKE :number')
            ->setParameter('number', $number)
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
