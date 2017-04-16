<?php

namespace AppBundle\Entity\Traits;

trait UniqueNameTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    protected $name;

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
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
