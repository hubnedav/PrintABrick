<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Part;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\CategoryRepository;
use AppBundle\Repository\LDraw\PartRepository;

class PartManager extends BaseManager
{
    /**
     * PartManager constructor.
     *
     * @param CategoryRepository $repository
     */
    public function __construct(PartRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Part entity with $number or retrieve one.
     *
     * @param $number
     *
     * @return Part
     */
    public function create($number)
    {
        if (($part = $this->repository->find($number)) == null) {
            $part = new Part();
            $part->setNumber($number);
        }

        return $part;
    }
}
