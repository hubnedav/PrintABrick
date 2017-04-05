<?php

namespace AppBundle\Api\Exception;

class EmptyResponseException extends ApiException
{
    /**
     * ApiException constructor.
     */
    public function __construct($service)
    {
        parent::__construct($service, 'flash.empty_response');
    }
}
