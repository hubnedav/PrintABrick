<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\SubpartRepository;

class SubpartManager extends BaseManager
{
    /**
     * SubpartManager constructor.
     *
     * @param SubpartRepository $repository
     */
    public function __construct(SubpartRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Subpart relation entity or retrieve one by foreign keys.
     *
     * @param $name
     *
     * @return Subpart
     */
    public function create($parent, $child)
    {
        if (($subpart = $this->repository->findOneByKeys($parent, $child))) {
            $subpart->setCount($subpart->getCount() + 1);
        } else {
            $subpart = new Subpart();
            $subpart
                ->setParent($parent)
                ->setSubpart($child)
                ->setCount(1);
        }

        return $subpart;
    }
}
