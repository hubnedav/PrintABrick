<?php

namespace AppBundle\Exception;


use Exception;

class ConvertingFailedException extends \Exception
{
    private $filepath;

    public function __construct($filepath = "", $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->filepath = $filepath;
    }

    /**
     * @return mixed
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * @param mixed $filepath
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
    }


}