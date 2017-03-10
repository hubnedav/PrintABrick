<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Set.
 *
 * @ORM\Table(name="set")
 * @ORM\Entity
 */
class Set
{
    //    /**
//     * @var int
//     *
//     * @ORM\Column(name="id", type="integer")
//     * @ORM\Id
//     * @ORM\GeneratedValue(strategy="AUTO")
//     */
//    private $id;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @var int
     *
     * @ORM\Column(name="part_count", type="integer", nullable=true)
     */
    private $partCount;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Part_Set", mappedBy="set")
     */
    private $parts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Rebrickable\Theme", mappedBy="sets")
     */
    private $themes;

    /**
     * Set constructor.
     */
    public function __construct()
    {
        $this->parts = new ArrayCollection();
        $this->themes = new ArrayCollection();
    }

//    /**
//     * Get id.
//     *
//     * @return int
//     */
//    public function getId()
//    {
//        return $this->id;
//    }

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
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param Part_Set $part
     *
     * @return Set
     */
    public function addPart(Part_Set $part)
    {
        $this->parts->add($part);

        return $this;
    }

    /**
     * @param Part_Set $part
     *
     * @return Set
     */
    public function removePart(Part_Set $part)
    {
        $this->parts->remove($part);

        return $this;
    }
}
