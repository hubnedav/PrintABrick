<?php

namespace AppBundle\Entity\Rebrickable;

use AppBundle\Entity\Traits\IdentityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part.
 *
 * @ORM\Table(name="rebrickable_inventory")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Rebrickable\InventoryRepository")
 */
class Inventory
{
    use IdentityTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $version;

    /**
     * @var Set
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Set", inversedBy="inventories")
     */
    protected $set;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory_Part", mappedBy="inventory")
     */
    protected $inventoryParts;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory_Set", mappedBy="inventory")
     */
    protected $inventorySets;

    public function __construct()
    {
        $this->inventoryParts = new ArrayCollection();
        $this->inventorySets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return Set
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * @param Set $set
     */
    public function setSet($set)
    {
        $this->set = $set;
    }

    /**
     * @return Collection
     */
    public function getInventoryParts()
    {
        return $this->inventoryParts;
    }

    /**
     * @param Inventory_Part $inventoryParts
     */
    public function setInventoryParts($inventoryParts)
    {
        $this->inventoryParts = $inventoryParts;
    }

    /**
     * @return Collection
     */
    public function getInventorySets()
    {
        return $this->inventorySets;
    }

    /**
     * @param Collection $inventorySets
     */
    public function setInventorySets($inventorySets)
    {
        $this->inventorySets = $inventorySets;
    }
}
