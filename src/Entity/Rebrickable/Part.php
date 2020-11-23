<?php

namespace App\Entity\Rebrickable;

use App\Entity\LDraw\Model;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\NumberTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part.
 *
 * @ORM\Table(name="rebrickable_part")
 * @ORM\Entity(repositoryClass="App\Repository\Rebrickable\PartRepository")
 */
class Part
{
    use NumberTrait;
    use NameTrait;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Rebrickable\Category", inversedBy="parts")
     */
    protected $category;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Rebrickable\Inventory_Part", mappedBy="part")
     */
    protected $inventoryParts;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\LDraw\Model", inversedBy="parts", fetch="EAGER")
     * @ORM\JoinTable(name="model_parts",
     *     joinColumns={@ORM\JoinColumn(name="part_id", referencedColumnName="id", unique=true)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id")}
     * )
     */
    private $model;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Rebrickable\PartRelationship", mappedBy="parent", fetch="EAGER")
     */
    private $childrenParts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Rebrickable\PartRelationship", mappedBy="children", fetch="EAGER")
     */
    private $parentParts;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30)
     */
    protected $material;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Rebrickable\Element", mappedBy="part")
     */
    private $elements;

    /**
     * Part constructor.
     */
    public function __construct()
    {
        $this->inventoryParts = new ArrayCollection();
        $this->childrenParts = new ArrayCollection();
        $this->parentParts = new ArrayCollection();
        $this->elements = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getInventoryParts()
    {
        return $this->inventoryParts;
    }

    /**
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
        return $this->model->first();
    }

    /**
     * @param Model $model
     */
    public function setModel($model): Part
    {
        $this->model->clear();
        $this->model->add($model);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChildrenParts($type = null)
    {
        if ($type) {
            $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('type', $type));

            return $this->childrenParts->matching($criteria);
        }

        return $this->childrenParts;
    }

    /**
     * @return ArrayCollection
     */
    public function getParentParts($type = null)
    {
        if ($type) {
            $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('type', $type));

            return $this->parentParts->matching($criteria);
        }

        return $this->parentParts;
    }

    /**
     * @return Collection|Element[]
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Element $element): self
    {
        if (!$this->elements->contains($element)) {
            $this->elements[] = $element;
            $element->setPart($this);
        }

        return $this;
    }

    public function removeElement(Element $element): self
    {
        if ($this->elements->contains($element)) {
            $this->elements->removeElement($element);
            // set the owning side to null (unless already changed)
            if ($element->getPart() === $this) {
                $element->setPart(null);
            }
        }

        return $this;
    }
}
