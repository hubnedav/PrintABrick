<?php

namespace AppBundle\Api\Client\Brickset\Entity;

class Instructions
{
    /**
     * @var string
     */
    protected $URL = null;

    /**
     * @var string
     */
    protected $description = null;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->URL;
    }

    /**
     * @param string $URL
     *
     * @return Instructions
     */
    public function setURL($URL)
    {
        $this->URL = $URL;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Instructions
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
