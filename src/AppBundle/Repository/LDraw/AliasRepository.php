<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Repository\BaseRepository;

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
        if (($alias = $this->findOneBy(['id' => $number, 'model' => $model])) == null) {
            $alias = new Alias();
            $alias->setModel($model);
            $alias->setId($number);
        }

        return $alias;
    }
}
