<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\AliasRepository;

class AliasManager extends BaseManager
{
    /**
     * AliasManager constructor.
     *
     * @param AliasRepository $repository
     */
    public function __construct(AliasRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Alias entity.
     *
     * @param $number
     * @param Model $model
     *
     * @return Alias
     */
    public function create($number, $model)
    {
        if (($alias = $this->repository->findOneBy(['number' => $number, 'model' => $model])) == null) {
            $alias = new Alias();
            $alias->setModel($model);
            $alias->setNumber($number);
        }

        return $alias;
    }
}
