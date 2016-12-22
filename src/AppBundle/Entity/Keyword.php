<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword
 *
 * @ORM\Table(name="keyword")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\KeywordRepository")
 */
class Keyword
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\BuildingKit", inversedBy="keywords")
     */
    private $building_kits;

    /**
     * Keyword constructor.
     */
    public function __construct()
    {
        $this->building_kits = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Keyword
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
     * @return ArrayCollection
     */
    public function getBuildingKits()
    {
        return $this->building_kits;
    }

    /**
     * @param BuildingKit $building_kit
     *
     * @return Keyword
     */
    public function addBuildingKit(BuildingKit $building_kit)
    {
        $this->building_kits->add($building_kit);

        return $this;
    }

    /**
     * @param BuildingKit $building_kit
     *
     * @return Keyword
     */
    public function removeBuildingKit(BuildingKit $building_kit)
    {
        $this->building_kits->remove($building_kit);

        return $this;
    }

}
