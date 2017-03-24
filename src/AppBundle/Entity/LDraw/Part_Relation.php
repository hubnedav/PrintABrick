<?php

namespace AppBundle\Entity\LDraw;

use Doctrine\ORM\Mapping as ORM;

/**
 * Part.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LDraw\Part_RelationRepository")
 * @ORM\Table(name="ldraw_part_relation")
 */
class Part_Relation
{
    /**
     * @var Part
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Part", inversedBy="relationsTo", cascade={"persist"})
     */
    private $parent;

    /**
     * @var Part
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LDraw\Part", inversedBy="relationsFrom", cascade={"persist"} )
     */
    private $child;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @return Part
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Part $parent
     *
     * @return Part_Relation
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Part
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * @param Part $child
     *
     * @return Part_Relation
     */
    public function setChild($child)
    {
        $this->child = $child;

        return $this;
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
     * @return Part_Relation
     */
    public function setCount($count)
    {
        $this->count = $count;

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
     *
     * @return Part_Relation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
