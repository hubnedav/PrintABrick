<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\ORM\Mapping as ORM;

/**
 * Part_Set.
 *
 * @ORM\Table(name="part_set")
 * @ORM\Entity
 */
class Part_Set
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Color", inversedBy="part_sets")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Part", inversedBy="sets" )
     */
    private $part;

    /**
     * @var Set
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Set", inversedBy="parts")
     */
    private $set;

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
     * @return Part_Set
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
     * @return Part_Set
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
     * @return Part_Set
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
     * @return Part_Set
     */
    public function setPart(Part $part)
    {
        $part->addSet($this);
        $this->part = $part;

        return $this;
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
     *
     * @return Part_Set
     */
    public function setSet(Set $set)
    {
        $set->addPart($this);
        $this->set = $set;

        return $this;
    }
}
