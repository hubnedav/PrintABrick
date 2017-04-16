<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\IdentityTrait;
use AppBundle\Entity\Traits\UniqueNameTrait;
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
    use UniqueNameTrait;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\LDraw\Model", mappedBy="keywords")
     */
    private $models;

    /**
     * Keyword constructor.
     */
    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

    /**
     * Get models.
     *
     * @return ArrayCollection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param Model $part
     *
     * @return Keyword
     */
    public function addModel(Model $part)
    {
        $this->models->add($part);

        return $this;
    }

    /**
     * @param Model $part
     *
     * @return Keyword
     */
    public function removeModel(Model $part)
    {
        $this->models->removeElement($part);

        return $this;
    }
}
