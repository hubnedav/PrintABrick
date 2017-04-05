<?php

namespace AppBundle\Api\Exception;

class CallFailedException extends ApiException
{
    /**
     * ApiException constructor.
     */
    public function __construct($service)
    {
        parent::__construct($service, 'flash.call_failed');
    }
}
