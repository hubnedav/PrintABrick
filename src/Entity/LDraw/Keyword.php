<?php

namespace App\Entity\LDraw;

use App\Entity\Traits\IdentityTrait;
use App\Entity\Traits\UniqueNameTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword.
 *
 * @ORM\Table(name="ldraw_model_keyword")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\KeywordRepository")
 */
class Keyword
{
    use IdentityTrait;
    use UniqueNameTrait;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\LDraw\Model", mappedBy="keywords")
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
     * @return Keyword
     */
    public function addModel(Model $part)
    {
        $this->models->add($part);

        return $this;
    }

    /**
     * @return Keyword
     */
    public function removeModel(Model $part)
    {
        $this->models->removeElement($part);

        return $this;
    }
}
