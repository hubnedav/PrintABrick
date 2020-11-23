<?php

namespace App\Entity\Rebrickable;

use App\Entity\Traits\IdentityTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category.
 *
 * @ORM\Table(name="rebrickable_category")
 * @ORM\Entity(repositoryClass="App\Repository\Rebrickable\CategoryRepository")
 */
class Category
{
    use IdentityTrait;
    use NameTrait;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Rebrickable\Part", mappedBy="category")
     */
    protected $parts;

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
     * @return Category
     */
    public function addPart(Part $part)
    {
        $this->parts->add($part);

        return $this;
    }

    /**
     * @return Category
     */
    public function removePart(Part $part)
    {
        $this->parts->remove($part);

        return $this;
    }
}
