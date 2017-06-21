<?php

namespace LoaderBundle\Exception;

use Throwable;

class ErrorParsingLineException extends FileException
{
    public function __construct($path, $line, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Error parsing line \""%s"\" "%s" file.', $line, $path);

        parent::__construct($path, $message, $code, $previous);
    }
}
