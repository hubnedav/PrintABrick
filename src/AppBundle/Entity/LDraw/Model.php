<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\NumberTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model.
 *
 * @ORM\Table(name="ldraw_model")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\ModelRepository")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Category", inversedBy="models", fetch="EAGER", cascade={"persist"})
     */
    private $category;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Alias", mappedBy="model", cascade={"persist","remove"})
     */
    private $aliases;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Subpart", mappedBy="parent", cascade={"persist"})
     */
    private $subparts;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LDraw\Subpart", mappedBy="subpart")
     */
    private $parents;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\LDraw\Keyword", inversedBy="models", cascade={"persist"})
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Author", cascade={"persist"}, inversedBy="models")
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Rebrickable\Part", mappedBy="model")
     */
    private $parts;

    public function __construct()
    {
        $this->keywords = new ArrayCollection();
        $this->subparts = new ArrayCollection();
        $this->parents = new ArrayCollection();
        $this->aliases = new ArrayCollection();
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
     */
    public function setPath($path)
    {
        $this->path = $path;
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
     * @param Category $category
     *
     * @return Model
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubparts()
    {
        return $this->subparts;
    }

    /**
     * @return ArrayCollection
     */
    public function getParents()
    {
        return $this->parents;
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
     * @return Model
     */
    public function addKeyword(Keyword $keyword)
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->addModel($this);
        }

        return $this;
    }

    /**
     * @param Keyword $keyword
     *
     * @return Model
     */
    public function removeKeyword(Keyword $keyword)
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @param Alias $alias
     *
     * @return $this
     */
    public function addAlias($alias)
    {
        if (!$this->aliases->contains($alias)) {
            $this->aliases->add($alias);
        }

        return $this;
    }

    /**
     * @param Subpart $subpart
     *
     * @return $this
     */
    public function addSubpart($subpart)
    {
        if (!$this->subparts->contains($subpart)) {
            $this->subparts->add($subpart);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getParts()
    {
        return $this->parts;
    }
}
