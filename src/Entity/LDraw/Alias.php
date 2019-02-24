<?php

namespace App\Entity\LDraw;

use App\Entity\Traits\NumberTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model.
 *
 * @ORM\Table(name="ldraw_alias")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\AliasRepository")
 */
class Alias
{
    use NumberTrait;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LDraw\Model", inversedBy="aliases")
     */
    private $model;

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }
}
