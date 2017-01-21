<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Part_BuildingKit.
 *
 * @ORM\Table(name="part__building_kit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Part_BuildingKitRepository")
 */
class Part_BuildingKit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    /**
     * @var Color
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Color", inversedBy="part_building_kits")
     */
    private $color;

    /**
     * @var bool
     *
     * @ORM\Column(name="type", type="boolean")
     */
    private $type;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Part", inversedBy="building_kits" )
     */
    private $part;

    /**
     * @var BuildingKit
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\BuildingKit", inversedBy="parts")
     */
    private $building_kit;

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
     * Set count.
     *
     * @param int $count
     *
     * @return Part_BuildingKit
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
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set color.
     *
     * @param Color $color
     *
     * @return Part_BuildingKit
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
     * @return Part_BuildingKit
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return bool
     */
    public function getType()
    {
        return $this->type;
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
     * @return Part_BuildingKit
     */
    public function setPart(Part $part)
    {
        $part->addBuildingKit($this);
        $this->part = $part;

        return $this;
    }

    /**
     * @return BuildingKit
     */
    public function getBuildingKit()
    {
        return $this->building_kit;
    }

    /**
     * @param BuildingKit $building_kit
     *
     * @return Part_BuildingKit
     */
    public function setBuildingKit(BuildingKit $building_kit)
    {
        $building_kit->addPart($this);
        $this->building_kit = $building_kit;

        return $this;
    }
}
