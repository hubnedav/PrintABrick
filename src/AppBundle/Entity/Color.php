<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Color.
 *
 * @ORM\Table(name="color")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ColorRepository")
 */
class Color
{
    use NameTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory_Part", mappedBy="color")
     */
    protected $inventoryParts;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Subpart", mappedBy="color")
     */
    protected $subparts;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->inventoryParts = new ArrayCollection();
        $this->subparts = new ArrayCollection();
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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param Inventory_Part $inventoryPart
     *
     * @return Color
     */
    public function addPartInventoryPart(Inventory_Part $inventoryPart)
    {
        $this->inventoryParts->add($inventoryPart);

        return $this;
    }

    /**
     * @param Inventory_Part $inventoryPart
     *
     * @return Color
     */
    public function removePartInventoryPart(Inventory_Part $inventoryPart)
    {
        $this->inventoryParts->remove($inventoryPart);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubparts()
    {
        return $this->subparts;
    }
}
