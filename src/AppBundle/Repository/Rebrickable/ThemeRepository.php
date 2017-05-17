<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class ThemeRepository extends BaseRepository
{
    public function findAllSubthemes(Theme $theme)
    {
        $queryBuilder = $this->createQueryBuilder('theme')
//            ->leftJoin(Theme::class, 'theme', Join::WITH, 'subtheme.parent = theme.id')
            ->where('theme.parent = :id')
            ->setParameter('id', $theme->getId());

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllMain()
    {
        $queryBuilder = $this->createQueryBuilder('theme')
            ->where('theme.parent IS NULL')
            ->orderBy('theme.name', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
