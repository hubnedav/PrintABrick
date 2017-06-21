<?php

namespace LoaderBundle\Exception;

use Exception;

class ConvertingFailedException extends \Exception
{
    private $filepath;

    public function __construct($form = '', $to = '', $message = '', $code = 0, Exception $previous = null)
    {
        $message = sprintf('Error converting "%s" file to "%s".', $form, $to);

        parent::__construct($message, $code, $previous);

        $this->filepath = $form;
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
