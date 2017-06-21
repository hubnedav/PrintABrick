<?php

namespace LoaderBundle\Exception\Loader;

use Symfony\Component\Form\Exception\LogicException;
use Throwable;

class MissingContextException extends LogicException
{
    public function __construct($context, $message = '', $code = 0, Throwable $previous = null)
    {
        $message = sprintf('"%s" context not found.', $context);

        parent::__construct($message, $code, $previous);
    }
}
