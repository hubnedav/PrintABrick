<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Color.
 *
 * @ORM\Table(name="rebrickable_color")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Rebrickable\ColorRepository")
 */
class Color
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="rgb", type="string", length=6, unique=false)
     */
    protected $rgb;

    /**
     * @var bool
     *
     * @ORM\Column(name="transparent", type="boolean")
     */
    protected $transparent;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory_part", mappedBy="color")
     */
    protected $inventoryParts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->inventoryParts = new ArrayCollection();
    }

    /**
     * Set id.
     *
     * @var int
     *
     * @return Color
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Color
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set rgb.
     *
     * @param string $rgb
     *
     * @return Color
     */
    public function setRgb($rgb)
    {
        $this->rgb = $rgb;

        return $this;
    }

    /**
     * Get rgb.
     *
     * @return string
     */
    public function getRgb()
    {
        return $this->rgb;
    }

    /**
     * Is transparent.
     *
     * @return bool
     */
    public function isTransparent()
    {
        return $this->transparent;
    }

    /**
     * Set transparent.
     *
     * @param bool $transparent
     */
    public function setTransparent($transparent)
    {
        $this->transparent = $transparent;
    }

    /**
     * @return Collection
     */
    public function getPartInventoryParts()
    {
        return $this->inventoryParts;
    }

    /**
     * @param Inventory_Part $part_building_kit
     *
     * @return Color
     */
    public function addPartInventoryPart(Inventory_Part $inventoryPart)
    {
        $this->inventoryParts->add($inventoryPart);

        return $this;
    }

    /**
     * @param Inventory_Part $part_building_kit
     *
     * @return Color
     */
    public function removePartInventoryPart(Inventory_Part $inventoryPart)
    {
        $this->inventoryParts->remove($inventoryPart);

        return $this;
    }
}
