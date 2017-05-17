<?php

namespace AppBundle\Entity\Traits;

trait NumberTrait
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     */
    protected $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
