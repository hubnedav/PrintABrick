<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Part_Relation;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\Part_RelationRepository;

class Part_RelationManager extends BaseManager
{
    /**
     * Part_RelationManager constructor.
     *
     * @param Part_RelationRepository $repository
     */
    public function __construct(Part_RelationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Keyword entity with $name or retrieve one.
     *
     * @param $name
     *
     * @return Part_Relation
     */
    public function create($parent, $child, $relationType)
    {
        if (($partRelation = $this->repository->findByForeignKeys($parent, $child, $relationType)) == null) {
            $partRelation = new Part_Relation();
            $partRelation
                ->setParent($parent)
                ->setChild($child)
                ->setCount(0)
                ->setType($relationType);
        }

        return $partRelation;
    }
}
