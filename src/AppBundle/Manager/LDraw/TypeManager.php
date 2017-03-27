<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Type;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\TypeRepository;

class TypeManager extends BaseManager
{
    /**
     * TypeManager constructor.
     *
     * @param TypeRepository $typeRepository
     */
    public function __construct(TypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Keyword entity with $name or retrieve one.
     *
     * @param $name
     *
     * @return Type
     */
    public function create($name)
    {
        if (($type = $this->repository->findByName($name)) == null) {
            $type = new Type();
            $type->setName($name);
        }

        return $type;
    }
}
