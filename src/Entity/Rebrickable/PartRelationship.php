<?php

namespace App\Entity\Rebrickable;

use Doctrine\ORM\Mapping as ORM;

/**
 * PartRelationship.
 *
 * @ORM\Table(name="rebrickable_part_relationships")
 * @ORM\Entity)
 */
class PartRelationship
{
    /**
     * @var Part
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Rebrickable\Part", inversedBy="childrenParts")
     */
    private $parent;

    /**
     * @var Part
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Rebrickable\Part", inversedBy="parentParts")
     */
    private $children;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=1)
     */
    private $type;

    public function getParent(): Part
    {
        return $this->parent;
    }

    public function getChildren(): Part
    {
        return $this->children;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): PartRelationship
    {
        $this->type = $type;

        return $this;
    }
}
