<?php

namespace App\Exception\RelationMapper;

use App\Exception\FileException;
use Throwable;

class ResourceNotFoundException extends FileException
{
    public function __construct($path, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Resource "%s" not found.', $path);

        parent::__construct($path, $message, $code, $previous);
    }
}
