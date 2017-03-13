<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Set.
 *
 * @ORM\Table(name="rebrickable_set")
 * @ORM\Entity
 */
class Set
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     */
    protected $number;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var int
     *
     * @ORM\Column(name="num_parts", type="integer", nullable=true)
     */
    protected $partCount;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Inventory", mappedBy="set")
     */
    protected $inventories;

    /**
     * @var Theme
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Rebrickable\Theme", inversedBy="sets")
     */
    protected $theme;

    /**
     * Set constructor.
     */
    public function __construct()
    {
        $this->inventories = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return Set
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Set
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
     * Set year.
     *
     * @param int $year
     *
     * @return Set
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getPartCount()
    {
        return $this->partCount;
    }

    /**
     * @param int $partCount
     *
     * @return Set
     */
    public function setPartCount($partCount)
    {
        $this->partCount = $partCount;

        return $this;
    }

    /**
     * Get parts.
     *
     * @return Collection
     */
    public function getInventories()
    {
        return $this->inventories;
    }

    /**
     * @param Inventory $inventory
     *
     * @return Set
     */
    public function addInventory(Inventory $inventory)
    {
        $this->inventories->add($inventory);

        return $this;
    }

    /**
     * @param Inventory $part
     *
     * @return Set
     */
    public function removeInventory(Inventory $inventory)
    {
        $this->inventories->removeElement($inventory);

        return $this;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param Theme $theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;
    }
}
