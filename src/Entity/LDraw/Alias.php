<?php

namespace App\Entity\LDraw;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="ldraw_alias")
 * @ORM\Entity(repositoryClass="App\Repository\LDraw\AliasRepository")
 */
class Alias extends Relation
{
    public const PATTERNED = 'P';
    public const ALTERNATE = 'A';

    /**
     * @ORM\Column(name="alias_type", type="string", length=1)
     */
    protected $type;

    /**
     * @return mixed
     */
    public function getAliasType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return Alias
     */
    public function setAliasType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getRelationType()
    {
        return self::TYPE_ALIAS;
    }
}
