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
    protected $number;

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return mixed
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }
}
