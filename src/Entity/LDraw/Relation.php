<?php

namespace App\Entity\LDraw;

use App\Entity\Color;
use App\Entity\Traits\IdentityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ldraw_model_relation", uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"parent_id", "child_id", "color_id", "relation_type"}),
 * })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="relation_type", type="string")
 * @ORM\DiscriminatorMap({
 *     Relation::TYPE_SUBPART = "Subpart",
 *     Relation::TYPE_ALIAS = "Alias"
 * })
 */
abstract class Relation
{
    const TYPE_SUBPART = 'S';
    const TYPE_ALIAS = 'A';

    use IdentityTrait;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LDraw\Model", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=false)
     */
    protected $parent;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\LDraw\Model", inversedBy="parents", cascade={"persist"})
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=false)
     */
    protected $child;

    public function getParent(): Model
    {
        return $this->parent;
    }

    public function setParent(Model $parent): Relation
    {
        $this->parent = $parent;

        return $this;
    }

    public function getChild(): Model
    {
        return $this->child;
    }

    public function setChild(Model $child): Relation
    {
        $this->child = $child;

        return $this;
    }
}
