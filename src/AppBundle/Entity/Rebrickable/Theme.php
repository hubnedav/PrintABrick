<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Theme.
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity
 */
class Theme
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Collection
     *
     * @ORM\ManyToOne(targetEntity="Theme")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Rebrickable\Set", inversedBy="themes")
     */
    private $sets;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return Collection
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getSets()
    {
        return $this->sets;
    }

    /**
     * @param Part_Set $set
     *
     * @return Theme
     */
    public function addSet(Part_Set $set)
    {
        $this->sets->add($set);

        return $this;
    }

    /**
     * @param Part_Set $set
     *
     * @return Theme
     */
    public function removeSet($set)
    {
        $this->sets->removeElement($set);

        return $this;
    }
}
