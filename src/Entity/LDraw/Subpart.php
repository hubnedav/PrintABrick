<?php

namespace App\Entity\LDraw;

use App\Entity\Color;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ldraw_subpart")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\SubpartRepository")
 */
class Subpart extends Relation
{
    /**
     * @var Color
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Color", inversedBy="subparts", cascade={"persist"})
     */
    private $color;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $count;

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

    public function getRelationType(): string
    {
        return self::TYPE_SUBPART;
    }
}
