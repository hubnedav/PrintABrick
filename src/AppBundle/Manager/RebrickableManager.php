<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Rebrickable\Theme;

class RebrickableManager extends BaseManager
{
    public function findAllThemes() {
        return $this->em->getRepository(Theme::class)->findAll();
    }
}
