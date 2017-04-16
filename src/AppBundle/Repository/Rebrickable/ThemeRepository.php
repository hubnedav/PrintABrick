<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class ThemeRepository extends BaseRepository
{
    public function findAllSubthemes(Theme $theme)
    {
        $subQueryBuilder = $this->createQueryBuilder('subtheme');

        $queryBuilder = $this->createQueryBuilder('subtheme')
            ->leftJoin(Theme::class, 'theme', Join::WITH, 'subtheme.parent = theme.id')
            ->where('subtheme.parent = :id')
            ->setParameter('id', $theme->getId());

        return $queryBuilder->getQuery()->getResult();
    }
}
