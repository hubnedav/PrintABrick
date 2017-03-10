<?php

namespace AppBundle\Entity\LDraw;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Type.
 *
 * @ORM\Table(name="ldraw_type")
 * @ORM\Entity
 */
class Type
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
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $name;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Part", mappedBy="type")
     */
    private $parts;

    /**
     * BuildingKit constructor.
     */
    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get parts.
     *
     * @return ArrayCollection
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param Part $part
     *
     * @return Type
     */
    public function addPart(Part $part)
    {
        $this->parts->add($part);

        return $this;
    }

    /**
     * @param Part $part
     *
     * @return Type
     */
    public function removePart(Part $part)
    {
        $this->parts->remove($part);

        return $this;
    }
}
