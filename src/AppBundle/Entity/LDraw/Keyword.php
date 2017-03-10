<?php

namespace AppBundle\Entity\LDraw;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword.
 *
 * @ORM\Table(name="ldraw_keyword")
 * @ORM\Entity
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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\LDraw\Part", mappedBy="keywords")
     */
    private $parts;

    /**
     * Keyword constructor.
     */
    public function __construct()
    {
        $this->parts = new ArrayCollection();
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
     * @return Keyword
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
     * Get models.
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
     * @return Keyword
     */
    public function addPart(Part $part)
    {
        $this->parts->add($part);

        return $this;
    }

    /**
     * @param Part $part
     *
     * @return Keyword
     */
    public function removeModel(Part $part)
    {
        $this->parts->removeElement($part);

        return $this;
    }
}
