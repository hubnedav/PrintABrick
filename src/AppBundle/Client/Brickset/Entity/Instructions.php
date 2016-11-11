<?php

namespace AppBundle\Client\Brickset\Entity;

class Instructions
{
    /**
     * @var string $URL
     */
    protected $URL = null;

    /**
     * @var string $description
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
     * @return Instructions
     */
    public function setDescription($description)
    {
      $this->description = $description;
      return $this;
    }

}
