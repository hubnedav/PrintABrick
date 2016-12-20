<?php

namespace AppBundle\Api\Client\Rebrickable\Entity;

class Set
{
    /**
     * Set ID if relevan to type.
     *
     * @var int
     */
    protected $set_id;
    /**
     * Set description.
     *
     * @var string
     */
    protected $name;
    /**
     * Number of sets the part appears in.
     *
     * @var int
     */
    protected $num_parts;

    /**
     * Set constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getSetId()
    {
        return $this->set_id;
    }

    /**
     * @param int $set_id
     */
    public function setSetId($set_id)
    {
        $this->set_id = $set_id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getNumParts()
    {
        return $this->num_parts;
    }

    /**
     * @param int $num_parts
     */
    public function setNumParts($num_parts)
    {
        $this->num_parts = $num_parts;
    }
}
