<?php

namespace App\Entity\LDraw;

use App\Entity\Traits\NumberTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model.
 *
 * @ORM\Table(name="ldraw_model")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\ModelRepository")
 */
class Model
{
    use NumberTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LDraw\Category", inversedBy="models", fetch="EAGER", cascade={"persist"})
     */
    private $category;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LDraw\Relation", mappedBy="parent", cascade={"all"}, fetch="EAGER")
     */
    private $children;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\LDraw\Relation", mappedBy="child", cascade={"all"})
     */
    private $parents;

    /**
     * @var ModelType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LDraw\ModelType", cascade={"persist"})
     */
    private $type;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\LDraw\Keyword", inversedBy="models", orphanRemoval=true, cascade={"persist"})
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var Author
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LDraw\Author", cascade={"persist"}, inversedBy="models")
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Rebrickable\Part", mappedBy="model")
     */
    private $parts;

    public function __construct()
    {
        $this->keywords = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->parts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Model
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set author.
     *
     * @param Author $author
     *
     * @return Model
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param \DateTime $modified
     *
     * @return Model
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
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
     *
     * @return Model
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return Model
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Relation[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getParents(): Collection
    {
        return $this->parents;
    }

    /**
     * @return $this
     */
    public function addParent(Relation $relation): Model
    {
        if (!$this->parents->contains($relation)) {
            $this->parents->add($relation);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addChild(Relation $relation): Model
    {
        if (!$this->children->contains($relation)) {
            $this->children->add($relation);
        }

        return $this;
    }

    public function getType(): ModelType
    {
        return $this->type;
    }

    public function setType(ModelType $type): Model
    {
        $this->type = $type;

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

    public function addKeyword(Keyword $keyword): Model
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->addModel($this);
        }

        return $this;
    }

    public function removeKeyword(Keyword $keyword): Model
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParts()
    {
        return $this->parts;
    }

    public function getAliases($type = null): Collection
    {
        return $this->parents
            ->filter(fn (Relation $r) => ($r instanceof Alias) && ($type ? $r->getAliasType() === $type : true) && ($r->getParent() !== $this))
//            ->map(fn (Relation $r) => $r->getParent())
            ;
    }

    public function getAliasOf($type = null): Collection
    {
        return $this->children
            ->filter(fn (Relation $r) => ($r instanceof Alias) && ($type ? $r->getAliasType() === $type : true) && ($r->getChild() !== $this))
            ->map(fn (Relation $r) => $r->getChild())
            ;
    }

    public function getSubpartOf(): Collection
    {
        return $this->parents
            ->filter(fn (Relation $r) => $r instanceof Subpart)
            ->map(fn (Relation $r) => $r->getParent());
    }

    public function getSubparts(): Collection
    {
        return $this->children
            ->filter(fn (Relation $r) => $r instanceof Subpart)
//            ->map(fn (Relation $r) => $r->getChild())
            ;
    }

    public function getSiblings(): Collection
    {
        $siblingRelations = $this->parents
            ->map(fn (Relation $r) => $r->getParent())
            ->map(fn (Model $p) => $p->getChildren())
            ->map(fn (Collection $c) => $c->toArray())
            ->toArray();

        return (new ArrayCollection(array_merge(...$siblingRelations)))
            ->filter(fn (Relation $r) => ($r instanceof Subpart) && ($r->getChild() !== $this))
            ->map(fn (Relation $r) => $r->getChild());
    }
}
