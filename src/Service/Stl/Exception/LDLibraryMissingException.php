<?php

namespace App\Service\Stl\Exception;

use Throwable;

class LDLibraryMissingException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($message = 'Invalid LDraw library.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
