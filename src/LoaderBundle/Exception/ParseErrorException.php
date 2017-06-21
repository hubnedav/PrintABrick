<?php

namespace LoaderBundle\Exception;

use Throwable;

class ParseErrorException extends FileException
{
    public function __construct($path, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Error parsing "%s" file.', $path);

        parent::__construct($path, $message, $code, $previous);
    }
}
