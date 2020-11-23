<?php

namespace App\Exception\Loader;

use Symfony\Component\Form\Exception\LogicException;
use Throwable;

class LoadingModelFailedException extends LogicException
{
    public function __construct($file, $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Loading "%s" failed.', $file);
        parent::__construct($message, $code, $previous);
    }
}
