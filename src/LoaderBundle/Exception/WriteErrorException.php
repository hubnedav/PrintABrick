<?php

namespace LoaderBundle\Exception;

use Throwable;

class WriteErrorException extends FileException
{
    public function __construct($file, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Could not write into "%s" file.', $file);

        parent::__construct($file, $message, $code, $previous);
    }
}
