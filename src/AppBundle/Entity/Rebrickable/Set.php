<?php

namespace AppBundle\Entity\Rebrickable;

use AppBundle\Entity\Traits\NameTrait;
use AppBundle\Entity\Traits\NumberTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Set.
 *
 * @ORM\Table(name="rebrickable_set")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Rebrickable\SetRepository")
 */
class Set
{
    use NumberTrait;
    use NameTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var int
     *
     * @ORM\Column(name="num_parts", type="integer", nullable=true)
     */
    protected $partCount;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory", mappedBy="set")
     */
    protected $inventories;

    /**
     * @var Theme
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Theme", inversedBy="sets", fetch="EAGER")
     */
    protected $theme;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory_Set", mappedBy="set")
     */
    protected $inventorySets;

    /**
     * Set constructor.
     */
    public function __construct()
    {
        $this->inventories = new ArrayCollection();
        $this->inventorySets = new ArrayCollection();
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return Set
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getPartCount()
    {
        return $this->partCount;
    }

    /**
     * @param int $partCount
     *
     * @return Set
     */
    public function setPartCount($partCount)
    {
        $this->partCount = $partCount;

        return $this;
    }

    /**
     * Get parts.
     *
     * @return Collection
     */
    public function getInventories()
    {
        return $this->inventories;
    }

    /**
     * @param Inventory $inventory
     *
     * @return Set
     */
    public function addInventory(Inventory $inventory)
    {
        $this->inventories->add($inventory);

        return $this;
    }

    /**
     * @param Inventory $inventory
     *
     * @return Set
     */
    public function removeInventory(Inventory $inventory)
    {
        $this->inventories->removeElement($inventory);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getInventorySets()
    {
        return $this->inventorySets;
    }

    /**
     * @param Inventory_Set $inventorySet
     */
    public function addInventorySet($inventorySet)
    {
        $this->inventorySets->add($inventorySet);
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }
}
