<?php

namespace AppBundle\Entity\Rebrickable;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Theme.
 *
 * @ORM\Table(name="rebrickable_theme")
 * @ORM\Entity
 */
class Theme
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var Theme
     *
     * @ORM\ManyToOne(targetEntity="Theme")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Set", mappedBy="theme")
     */
    protected $sets;

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
     * @return Theme
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
     * @param Set $set
     *
     * @return Theme
     */
    public function addSet(Set $set)
    {
        $this->sets->add($set);

        return $this;
    }

    /**
     * @param Set $set
     *
     * @return Theme
     */
    public function removeSet($set)
    {
        $this->sets->removeElement($set);

        return $this;
    }
}
