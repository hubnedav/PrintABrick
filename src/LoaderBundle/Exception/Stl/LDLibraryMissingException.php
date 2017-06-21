<?php

namespace LoaderBundle\Exception\Stl;

use Symfony\Component\Form\Exception\LogicException;
use Throwable;

class LDLibraryMissingException extends LogicException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $message = 'Invalid LDraw library.';

        parent::__construct($message, $code, $previous);
    }
}
