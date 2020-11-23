<?php

namespace App\Service;

use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Model;
use App\Entity\LDraw\Relation;
use App\Entity\LDraw\Subpart;
use App\Repository\LDraw\ModelRepository;

class ModelService
{
    private array $models = [];
    private ModelRepository $modelRepository;

    /**
     * ModelService constructor.
     */
    public function __construct(ModelRepository $modelRepository)
    {
        $this->modelRepository = $modelRepository;
    }

    /**
     * Get all subparts of a model.
     *
     * @return array
     */
    public function getSubmodels(Model $model)
    {
        return $model->getChildren()->map(fn (Relation $relation) => $relation->getChild());

//        foreach ($model->getChildren() as $child) {
//            if($child instanceof Subpart) {
//                $this->resursiveLoadChildren($child->getChild(), $child->getCount());
//            }
//        }
//
//        return $this->models;
    }

    /**
     * Get all parents of a model.
     *
     * @return array
     */
    public function getParents(Model $model)
    {
        return $model->getParents()->filter(fn (Relation $r) => $r instanceof Alias)->map(fn (Relation $r) => $r->getParent());

//        return $this->modelRepository->findAllParents($model);
    }

    /**
     * Get all siblings of a model.
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
        return $this->modelRepository->count(['type' => 1]);
    }

    private function resursiveLoadChildren(Model $model, $quantity = 1, $color = -1)
    {
        if (0 !== $model->getChildren()->count()) {
            foreach ($model->getChildren() as $child) {
                if ($child instanceof Subpart) {
                    $this->resursiveLoadChildren($child->getChild(), $child->getCount(), $child->getColor());
                }
            }
        } else {
            $q = $this->models[$model->getId()]['quantity'] ?? 0;

            $this->models[$model->getId()] = [
                'quantity' => $q + $quantity,
                'model' => $model,
                'color' => $color,
            ];
        }
    }
}
