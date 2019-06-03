<?php

namespace App\Entity\Rebrickable;

use App\Entity\Traits\IdentityTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Theme.
 *
 * @ORM\Table(name="rebrickable_theme")
 * @ORM\Entity(repositoryClass="App\Repository\Rebrickable\ThemeRepository")
 */
class Theme
{
    use IdentityTrait;
    use NameTrait;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Rebrickable\Set", mappedBy="theme")
     */
    protected $sets;

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

    public function getFullName()
    {
        $theme = $this;
        $name = [];

        do {
            $name[] = $theme->getName();
        } while (null !== ($theme = $theme->getParent()));

        return implode(' > ', array_reverse($name));
    }

    public function getGroup()
    {
        $theme = $this;
        while (null !== ($theme->getParent())) {
            $theme = $theme->getParent();
        }

        return $theme;
    }
}
