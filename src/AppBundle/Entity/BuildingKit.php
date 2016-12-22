<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BuildingKit
 *
 * @ORM\Table(name="building_kit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BuildingKitRepository")
 */
class BuildingKit
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
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255, unique=true)
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Part_BuildingKit", mappedBy="building_kit")
     */
    private $parts;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Keyword", mappedBy="building_kits")
     */
    private $keywords;

    /**
     * BuildingKit constructor.
     */
    public function __construct()
    {
        $this->parts = new ArrayCollection();
        $this->keywords = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return BuildingKit
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return BuildingKit
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return BuildingKit
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
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
     * @return BuildingKit
     */
    public function setPartCount($partCount)
    {
        $this->partCount = $partCount;

        return $this;
    }

    /**
     * Get parts
     *
     * @return Collection
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param Part_BuildingKit $part
     *
     * @return BuildingKit
     */
    public function addPart(Part_BuildingKit $part)
    {
        $this->parts->add($part);

        return $this;
    }

    /**
     * @param Part_BuildingKit $part
     *
     * @return BuildingKit
     */
    public function removePart(Part_BuildingKit $part)
    {
        $this->parts->remove($part);

        return $this;
    }

    /**
     * Get keywords
     *
     * @return Collection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param Keyword $keyword
     *
     * @return BuildingKit
     */
    public function addKeyword(Keyword $keyword)
    {
        $this->keywords->add($keyword);

        return $this;
    }

    /**
     * @param Keyword $keyword
     *
     * @return BuildingKit
     */
    public function removeKeyword(Keyword $keyword)
    {
        $this->keywords->remove($keyword);

        return $this;
    }
}
