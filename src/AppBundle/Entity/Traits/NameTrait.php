<?php

namespace AppBundle\Entity\Traits;

trait NameTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
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
