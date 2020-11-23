<?php

namespace App\Entity\Rebrickable;

use App\Entity\Color;
use Doctrine\ORM\Mapping as ORM;

/**
 * Inventory_Part.
 *
 * @ORM\Table(name="rebrickable_inventory_parts")
 * @ORM\Entity(repositoryClass="App\Repository\Rebrickable\Inventory_PartRepository")
 */
class Inventory_Part
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @var Color
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Color", inversedBy="inventoryParts", fetch="EAGER")
     */
    protected $color;

    /**
     * @var bool
     * @ORM\Id
     * @ORM\Column(type="boolean")
     */
    protected $spare;

    /**
     * @var Part
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Rebrickable\Part", inversedBy="inventoryParts", fetch="EAGER")
     */
    protected $part;

    /**
     * @var Inventory
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Rebrickable\Inventory", inversedBy="inventoryParts")
     */
    protected $inventory;

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
     * Set color.
     *
     * @param Color $color
     *
     * @return Inventory_Part
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set type.
     *
     * @param bool $spare
     *
     * @return Inventory_Part
     */
    public function setSpare($spare)
    {
        $this->spare = $spare;

        return $this;
    }

    /**
     * Get type.
     *
     * @return bool
     */
    public function isSpare()
    {
        return $this->spare;
    }

    /**
     * @return Part
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @return Inventory_Part
     */
    public function setPart(Part $part)
    {
        $this->part = $part;

        return $this;
    }

    /**
     * @return Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @return Inventory_Part
     */
    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }
}
