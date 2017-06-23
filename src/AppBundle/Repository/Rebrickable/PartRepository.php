<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Category;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class PartRepository extends BaseRepository
{
    public function findAllNotPaired()
    {
        $queryBuilder = $this->createQueryBuilder('part')
            ->leftJoin(Category::class, 'category', JOIN::WITH, 'part.category = category.id')
            ->where('part.model IS NULL')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }
}
