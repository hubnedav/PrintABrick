<?php

namespace AppBundle\Api\Exception;

class AuthenticationFailedException extends ApiException
{
    /**
     * ApiException constructor.
     */
    public function __construct($service)
    {
        parent::__construct($service, 'flash.authentication_failed');
    }
}
