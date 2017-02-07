<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Color.
 *
 * @ORM\Table(name="color")
 * @ORM\Entity
 */
class Color
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="rgb", type="string", length=6, unique=false)
     */
    private $rgb;

    /**
     * @var bool
     *
     * @ORM\Column(name="transparent", type="boolean")
     */
    private $transparent;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Part_Set", mappedBy="color")
     */
    private $part_sets;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->part_sets = new ArrayCollection();
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
    public function getPartBuildingKits()
    {
        return $this->part_sets;
    }

    /**
     * @param Part_Set $part_building_kit
     *
     * @return Color
     */
    public function addPartBuildingKit(Part_Set $part_set)
    {
        $this->part_sets->add($part_set);

        return $this;
    }

    /**
     * @param Part_Set $part_building_kit
     *
     * @return Color
     */
    public function removePartBuildingKit(Part_Set $part_set)
    {
        $this->part_sets->remove($part_set);

        return $this;
    }
}
