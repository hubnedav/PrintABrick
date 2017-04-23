<?php

namespace AppBundle\Entity\LDraw;

use AppBundle\Entity\Color;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subpart.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\SubpartRepository")
 * @ORM\Table(name="ldraw_subpart")
 */
class Subpart
{
    /**
     * @var Model
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Model", inversedBy="subparts")
     */
    private $parent;

    /**
     * @var Model
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Model", inversedBy="parents", cascade={"persist"} )
     */
    private $subpart;

    /**
     * @var Color
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Color", inversedBy="subparts")
     */
    private $color;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @return Model
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Model $parent
     *
     * @return Subpart
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Model
     */
    public function getSubpart()
    {
        return $this->subpart;
    }

    /**
     * @param Model $subpart
     *
     * @return Subpart
     */
    public function setSubpart($subpart)
    {
        $this->subpart = $subpart;

        return $this;
    }

    /**
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param Color $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return Subpart
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }
}
