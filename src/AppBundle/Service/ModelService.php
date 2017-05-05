<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;

class ModelService
{
    private $models = [];

    public function getAllSubparts(Model $model)
    {
        foreach ($model->getSubparts() as $subpart) {
            $this->resursiveLoadModels($subpart->getSubpart(), $subpart->getCount());
        }

        return $this->models;
    }

    private function resursiveLoadModels(Model $model, $quantity = 1)
    {
        if (($model->getSubparts()->count() !== 0)) {
            foreach ($model->getSubparts() as $subpart) {
                $this->resursiveLoadModels($subpart->getSubpart(), $subpart->getCount());
            }
        } else {
            $q = isset($this->models[$model->getNumber()]['quantity']) ? $this->models[$model->getNumber()]['quantity'] : 0;

            $this->models[$model->getNumber()] = [
                'quantity' => $q + $quantity,
                'model' => $model,
            ];
        }
    }
}
