<?php

namespace App\Service\Stl\Exception;

use Exception;

class ConversionFailedException extends \RuntimeException implements ExceptionInterface
{
    private $filepath;

    public function __construct($from = '', $to = '', Exception $previous = null)
    {
        $message = sprintf('Error converting "%s" file to "%s".', $from, $to);

        parent::__construct($message, 0, $previous);

        $this->filepath = $from;
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
