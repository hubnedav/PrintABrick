<?php

namespace AppBundle\Entity;

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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Part_BuildingKit", mappedBy="color")
     */
    private $part_building_kits;

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
     * @return Collection
     */
    public function getPartBuildingKits()
    {
        return $this->part_building_kits;
    }

    /**
     * @param Part_BuildingKit $part_building_kit
     *
     * @return Color
     */
    public function addPartBuildingKit(Part_BuildingKit $part_building_kit)
    {
        $this->part_building_kits->add($part_building_kit);

        return $this;
    }

    /**
     * @param Part_BuildingKit $part_building_kit
     *
     * @return Color
     */
    public function removePartBuildingKit(Part_BuildingKit $part_building_kit)
    {
        $this->part_building_kits->remove($part_building_kit);

        return $this;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->part_building_kits = new \Doctrine\Common\Collections\ArrayCollection();
    }
}
