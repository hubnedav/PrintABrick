<?php

namespace App\Entity\Rebrickable;

use App\Entity\Color;
use App\Entity\Traits\NumberTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Element.
 *
 * @ORM\Table(name="rebrickable_element")
 * @ORM\Entity(repositoryClass="App\Repository\Rebrickable\ElementRepository")
 */
class Element
{
    use NumberTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rebrickable\Part", inversedBy="elements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $part;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Color")
     * @ORM\JoinColumn(nullable=false)
     */
    private $color;

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(?Part $part): self
    {
        $this->part = $part;

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }
}
