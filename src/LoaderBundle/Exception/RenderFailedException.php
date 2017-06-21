<?php

namespace LoaderBundle\Exception;

use Throwable;

class RenderFailedException extends FileException
{
    public function __construct($path, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Error rendering "%s" file.', $path);

        parent::__construct($path, $message, $code, $previous);
    }
}
