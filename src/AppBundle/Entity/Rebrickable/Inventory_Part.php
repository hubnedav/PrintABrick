<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inventory_Part.
 *
 * @ORM\Table(name="rebrickable_inventory_parts")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Rebrickable\Inventory_PartRepository")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Color", inversedBy="inventoryParts")
     */
    protected $color;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $isSpare;

    /**
     * @var Part
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Part", inversedBy="inventoryParts")
     */
    protected $part;

    /**
     * @var Inventory
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Inventory", inversedBy="inventoryParts")
     */
    protected $inventory;

    /**
     * Set count.
     *
     * @param int $count
     *
     * @return Inventory_Part
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
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
     * @param bool $type
     *
     * @return Inventory_Part
     */
    public function setIsSpare($isSpare)
    {
        $this->isSpare = $isSpare;

        return $this;
    }

    /**
     * Get type.
     *
     * @return bool
     */
    public function isSpare()
    {
        return $this->isSpare();
    }

    /**
     * @return Part
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @param Part $part
     *
     * @return Inventory_Part
     */
    public function setPart(Part $part)
    {
        $this->part = $part;

        return $this;
    }

    /**
     * @return Set
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param Inventory $inventory
     *
     * @return Inventory_Part
     */
    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }
}
