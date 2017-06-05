<?php

namespace AppBundle\Entity\Rebrickable;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Traits\NameTrait;
use AppBundle\Entity\Traits\NumberTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part.
 *
 * @ORM\Table(name="rebrickable_part")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Rebrickable\PartRepository")
 */
class Part
{
    use NumberTrait;
    use NameTrait;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Category", inversedBy="parts")
     */
    protected $category;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory_Part", mappedBy="part")
     */
    protected $inventoryParts;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Model", inversedBy="parts", fetch="EAGER")
     */
    private $model;

    /**
     * Part constructor.
     */
    public function __construct()
    {
        $this->inventoryParts = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getInventoryParts()
    {
        return $this->inventoryParts;
    }

    /**
     * @param Inventory_Part $inventoryPart
     *
     * @return Part
     */
    public function addInventoryPart(Inventory_Part $inventoryPart)
    {
        $this->inventoryParts->add($inventoryPart);

        return $this;
    }

    /**
     * @param Inventory_Part $inventoryPart
     *
     * @return Part
     */
    public function removeInventoryPart($inventoryPart)
    {
        $this->inventoryParts->removeElement($inventoryPart);

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return Part
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param Model $model
     *
     * @return Part
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }
}
