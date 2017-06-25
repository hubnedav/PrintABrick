<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inventory_Set.
 *
 * @ORM\Table(name="rebrickable_inventory_sets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Rebrickable\Inventory_SetRepository")
 */
class Inventory_Set
{
    /**
     * @var Inventory
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Inventory", inversedBy="inventorySets")
     */
    protected $inventory;

    /**
     * @var Set
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Set", inversedBy="inventorySets")
     */
    protected $set;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get count.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
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
     * @return Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param Inventory $inventory
     *
     * @return Inventory_Set
     */
    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }
}
