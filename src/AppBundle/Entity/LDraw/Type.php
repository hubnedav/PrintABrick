<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\IdentityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Type.
 *
 * @ORM\Table(name="ldraw_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\TypeRepository")
 */
class Type
{
    use IdentityTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $name;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Model", mappedBy="type")
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Type
     */
    public function addModel(Model $model)
    {
        $this->models->add($model);

        return $this;
    }

    /**
     * @param Model $model
     *
     * @return Type
     */
    public function removeModel(Model $model)
    {
        $this->models->remove($model);

        return $this;
    }
}
