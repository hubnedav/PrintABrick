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
    public function create($parent, $child, $count)
    {
//        if (($subpart = $this->repository->findOneByKeys($parent, $child))) {
//            $subpart->setCount($count);
//        } else {
            $subpart = new Subpart();
            $subpart
                ->setParent($parent)
                ->setSubpart($child)
                ->setCount($count);
//        }

        return $subpart;
    }
}
