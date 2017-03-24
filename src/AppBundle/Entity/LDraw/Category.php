<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\IdentityTrait;
use AppBundle\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * @ORM\Table(name="ldraw_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\CategoryRepository")
 */
class Category
{
    use IdentityTrait;
    use NameTrait;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Part", mappedBy="category")
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
     * @return Category
     */
    public function addPart(Part $part)
    {
        $this->parts->add($part);

        return $this;
    }

    /**
     * @param Part $part
     *
     * @return Category
     */
    public function removePart(Part $part)
    {
        $this->parts->remove($part);

        return $this;
    }
}
