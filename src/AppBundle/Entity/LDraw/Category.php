<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\IdentityTrait;
use AppBundle\Entity\Traits\UniqueNameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * @ORM\Table(name="ldraw_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\CategoryRepository")
 */
class Category
{
    use IdentityTrait;
    use UniqueNameTrait;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Model", mappedBy="category")
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
     * @param Model $model
     *
     * @return Category
     */
    public function addModel(Model $model)
    {
        $this->models->add($model);

        return $this;
    }

    /**
     * @param Model $model
     *
     * @return Category
     */
    public function removeModel(Model $model)
    {
        $this->models->remove($model);

        return $this;
    }
}
