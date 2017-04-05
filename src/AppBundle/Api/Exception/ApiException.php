<?php

namespace AppBundle\Api\Exception;

class ApiException extends \Exception
{
    const BRICKSET = 'Brickset';
    const REBRICKABLE = 'Rebrickable';

    /**
     * @var string
     */
    private $service;

    /**
     * ApiException constructor.
     */
    public function __construct($service = 'unknownService', $message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param string $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }
}
