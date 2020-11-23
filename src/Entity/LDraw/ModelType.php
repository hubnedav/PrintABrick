<?php

namespace App\Entity\LDraw;

use App\Entity\Traits\IdentityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ldraw_model_type")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\ModelTypeRepository")
 */
class ModelType
{
    public const ALIAS = 'Alias';
    public const PART = 'Part';
    public const PART_FLEXIBLE = 'Part Flexible_Section';
    public const PART_COLOUR = 'Part Physical_Colour';
    public const PRINTED = 'Printed';
    public const SHORTCUT = 'Shortcut';
    public const SHORTCUT_COLOUR = 'Shortcut Physical_Colour';

    use IdentityTrait;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     */
    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
