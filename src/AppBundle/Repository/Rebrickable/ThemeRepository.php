<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\Rebrickable\Theme;
use AppBundle\Repository\BaseRepository;

class ThemeRepository extends BaseRepository
{
    public function findAllSubthemes(Theme $theme)
    {
        $queryBuilder = $this->createQueryBuilder('theme')
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
