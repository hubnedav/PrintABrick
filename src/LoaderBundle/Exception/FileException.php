<?php

namespace LoaderBundle\Exception;

use Throwable;

class FileException extends \Exception
{
    protected $path;

    public function __construct($path, $message = '', $code = 0, Throwable $previous = null)
    {
        $this->path = $path;

        parent::__construct($message, $code, $previous);
    }
}
