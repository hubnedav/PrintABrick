<?php

namespace LoaderBundle\Exception\Loader;

use Symfony\Component\Form\Exception\LogicException;
use Throwable;

class LoadingModelFailedException extends LogicException
{
    public function __construct($file, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Loading "%s" failed.', $file);

        parent::__construct($message, $code, $previous);
    }
}
