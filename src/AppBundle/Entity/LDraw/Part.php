<?php

namespace AppBundle\Entity\LDraw;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Part.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\PartRepository")
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
     * @var Part_Relation
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Part_Relation", mappedBy="parent")
     */
    private $relationsTo;

    /**
     * @var Part_Relation
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Part_Relation", mappedBy="child")
     */
    private $relationsFrom;

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
        $this->relationsTo = new ArrayCollection();
        $this->relationsFrom = new ArrayCollection();
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
     * @return Part_Relation
     */
    public function getRelationsTo()
    {
        return $this->relationsTo;
    }

    /**
     * @param Part_Relation $relationsTo
     */
    public function setRelationsTo($relationsTo)
    {
        $this->relationsTo = $relationsTo;
    }

    /**
     * @return Part_Relation
     */
    public function getRelationsFrom()
    {
        return $this->relationsFrom;
    }

    /**
     * @param Part_Relation $relationsFrom
     */
    public function setRelationsFrom($relationsFrom)
    {
        $this->relationsFrom = $relationsFrom;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        if (!$this->model) {
            if ($this->getPrintOf()) {
                return $this->getPrintOf()->getModel();
            } elseif ($this->getAliasOf()) {
                return $this->getAliasOf()->getModel();
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
    public function removeKeyword(Keyword $keyword)
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }

    private function getRelationOf($type)
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('type', $type));

        $relations = $this->relationsFrom->matching($criteria);

        $array = new ArrayCollection();
        foreach ($relations as $relation) {
            $array->add($relation->getParent());
        }

        return $array;
    }

    private function getRelations($type)
    {
        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->eq('type', $type));

        $relations = $this->relationsTo->matching($criteria);

        $array = new ArrayCollection();
        foreach ($relations as $relation) {
            $array->add($relation->getChild());
        }

        return $array;
    }

    public function getPrintOf()
    {
        $parents = $this->getRelationOf('Print');
        if (count($parents) > 0) {
            return $parents->first();
        }

        return null;
    }

    public function getPrints()
    {
        return $this->getRelations('Print');
    }

    public function getSubpartOf()
    {
        return $this->getRelationOf('Subpart');
    }

    public function getSubparts()
    {
        return $this->getRelations('Subpart');
    }

    public function getAliasOf()
    {
        $parents = $this->getRelationOf('Alias');
        if (count($parents) > 0) {
            return $parents->first();
        }

        return null;
    }

    public function getAliases()
    {
        return $this->getRelations('Alias');
    }
}
