<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\ModelRepository;

class ModelManager extends BaseManager
{
    /**
     * ModelManager constructor.
     *
     * @param ModelRepository $repository
     */
    public function __construct(ModelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Model entity with $number or retrieve one.
     *
     * @param $number
     *
     * @return Model
     */
    public function create($number)
    {
        if (($model = $this->repository->findOneBy(['number' => $number])) == null) {
            $model = new Model();
            $model->setNumber($number);
        }

        return $model;
    }

    public function findByNumber($number)
    {
        return $this->repository->findOneByNumber($number);
    }

    public function findByName($name)
    {
        return $this->repository->findOneBy(['name' => $name]);
    }
}
