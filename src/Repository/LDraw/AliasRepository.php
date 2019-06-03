<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Alias;
use App\Entity\LDraw\Model;
use App\Repository\BaseRepository;

class AliasRepository extends BaseRepository
{
    /**
     * Get existing entity or create new.
     *
     * @param $number
     * @param Model $model
     *
     * @return Alias
     */
    public function getOrCreate($number, $model)
    {
        if (null == ($alias = $this->findOneBy(['id' => $number, 'model' => $model]))) {
            $alias = new Alias();
            $alias->setModel($model);
            $alias->setId($number);
        }

        return $alias;
    }
}
