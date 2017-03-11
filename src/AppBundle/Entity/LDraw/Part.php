<?php

namespace AppBundle\Entity\LDraw;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part.
 *
 * @ORM\Entity
 * @ORM\Table(name="ldraw_part")
 */
class Part
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $id;

    /**
     * @var Type
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Type", inversedBy="parts", cascade={"persist"})
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Category", inversedBy="parts", cascade={"persist"})
     */
    private $category;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Part",cascade={"persist"})
     */
    private $printOf;

    /**
     * @var Part
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Part",cascade={"persist"})
     */
    private $aliasOf;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Model", cascade={"persist"})
     */
    private $model;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\LDraw\Keyword", inversedBy="parts", cascade={"persist"})
     */
    private $keywords;

    public function __construct()
    {
        $this->keywords = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Part
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Collection $category
     *
     * @return Part
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Part
     */
    public function getPrintOf()
    {
        return $this->printOf;
    }

    /**
     * @param Part $printOf
     *
     * @return Part
     */
    public function setPrintOf($printOf)
    {
        $this->printOf = $printOf;

        return $this;
    }

    /**
     * @return Part
     */
    public function getAliasOf()
    {
        return $this->aliasOf;
    }

    /**
     * @param Part $printOf
     *
     * @return Part
     */
    public function setAliasOf($aliasOf)
    {
        $this->aliasOf = $aliasOf;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        if (!$this->model) {
            if ($this->printOf) {
                return $this->printOf->getModel();
            } elseif ($this->aliasOf) {
                return $this->aliasOf->getModel();
            }
            return null;
        }

        return $this->model;
    }

    /**
     * @param Model $model
     *
     * @return Part
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get keywords.
     *
     * @return Collection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param Keyword $keyword
     *
     * @return Part
     */
    public function addKeyword(Keyword $keyword)
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->addPart($this);
        }

        return $this;
    }

    /**
     * @param Keyword $keyword
     *
     * @return Part
     */
    public function removePart(Keyword $keyword)
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }
}
