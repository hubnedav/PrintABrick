<?php

namespace LoaderBundle\Exception;

use Throwable;

class FileNotFoundException extends FileException
{
    public function __construct($path, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('File "%s" not found.', $path);

        parent::__construct($path, $message, $code, $previous);
    }
}
