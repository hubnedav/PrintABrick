<?php

namespace App\Entity\LDraw;

use App\Entity\Traits\IdentityTrait;
use App\Entity\Traits\UniqueNameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * @ORM\Table(name="ldraw_category")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\CategoryRepository")
 */
class Category
{
    use IdentityTrait;
    use UniqueNameTrait;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LDraw\Model", mappedBy="category")
     */
    private $models;

    /**
     * BuildingKit constructor.
     */
    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

    /**
     * Get models.
     *
     * @return ArrayCollection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @return Category
     */
    public function addModel(Model $model)
    {
        $this->models->add($model);

        return $this;
    }

    /**
     * @return Category
     */
    public function removeModel(Model $model)
    {
        $this->models->remove($model);

        return $this;
    }
}
