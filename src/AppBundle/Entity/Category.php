<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
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
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Model", mappedBy="category")
     */
    private $models;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Part", mappedBy="category")
     */
    private $parts;

    /**
     * BuildingKit constructor.
     */
    public function __construct()
    {
        $this->models = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get models
     *
     * @return ArrayCollection
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param Model $model
     *
     * @return Category
     */
    public function addModel(Model $model)
    {
        $this->models->add($model);

        return $this;
    }

    /**
     * @param Model $model
     *
     * @return Category
     */
    public function removeModel(Model $model)
    {
        $this->models->remove($model);

        return $this;
    }

    /**
     * Get parts
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
