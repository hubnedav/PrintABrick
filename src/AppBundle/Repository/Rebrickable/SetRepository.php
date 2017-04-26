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
    public function findAllByTheme(Theme $theme)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join(Theme::class, 'theme', Join::WITH, 's.theme = theme')
            ->where('theme.id = :id')
            ->setParameter('id', $theme->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllByPartNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory.set = s.number')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'inventory.id = inventory_part.inventory')
            ->join(Part::class, 'part', Join::WITH, 'inventory_part.part = part.number')
            ->where('part.number LIKE :number')
            ->setParameter('number', $number)
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllByModel(Model $model)
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory.set = s.number')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'inventory.id = inventory_part.inventory')
            ->join(Part::class, 'part', Join::WITH, 'inventory_part.part = part.number')
            ->where('part.model = :model')
            ->setParameter('model', $model->getNumber())
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByQuery($query, $limit = null) {
        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.name LIKE :name')
            ->orWhere('s.number LIKE :number')
            ->setParameter('name', '%'.$query.'%')
            ->setParameter('number', $query.'%');

        if($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
