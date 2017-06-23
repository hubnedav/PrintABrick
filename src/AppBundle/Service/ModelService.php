<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Repository\LDraw\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;

class ModelService
{
    private $models = [];

    /** @var ModelRepository */
    private $modelRepository;

    /**
     * ModelService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->modelRepository = $em->getRepository(Model::class);
    }

    /**
     * Find model by id or alias number.
     *
     * @param $id
     *
     * @return Model|null
     */
    public function find($id)
    {
        return $this->modelRepository->findOneByNumber($id);
    }

    /**
     * Get all subparts of model.
     *
     * @param Model $model
     *
     * @return array
     */
    public function getSubmodels(Model $model)
    {
        foreach ($model->getSubparts() as $subpart) {
            $this->resursiveLoadModels($subpart->getSubpart(), $subpart->getCount());
        }

        return $this->models;
    }

    /**
     * Get all siblings of model.
     *
     * @param Model $model
     *
     * @return array
     */
    public function getSiblings(Model $model)
    {
        return $this->modelRepository->findAllSiblings($model);
    }

    /**
     * Get total count of models in database.
     *
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->modelRepository->count();
    }

    private function resursiveLoadModels(Model $model, $quantity = 1)
    {
        if (($model->getSubparts()->count() !== 0)) {
            foreach ($model->getSubparts() as $subpart) {
                $this->resursiveLoadModels($subpart->getSubpart(), $subpart->getCount());
            }
        } else {
            $q = isset($this->models[$model->getId()]['quantity']) ? $this->models[$model->getId()]['quantity'] : 0;

            $this->models[$model->getId()] = [
                'quantity' => $q + $quantity,
                'model' => $model,
            ];
        }
    }
}
