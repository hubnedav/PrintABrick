<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Traits\NumberTrait;
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
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modified;

//    /**
//     * @var Collection
//     *
//     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Part", mappedBy="model")
//     */
//    private $parts;

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return Model
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Set author.
     *
     * @param string $author
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
     * @return string
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
}
