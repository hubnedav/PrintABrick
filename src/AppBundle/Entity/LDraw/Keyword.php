<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\IdentityTrait;
use AppBundle\Entity\Traits\NameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword.
 *
 * @ORM\Table(name="ldraw_keyword")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\KeywordRepository")
 */
class Keyword
{
    use IdentityTrait;
    use NameTrait;

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
