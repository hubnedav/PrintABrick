<?php

namespace AppBundle\Api\Manager;

use AppBundle\Api\Client\Rebrickable\Rebrickable;

class RebrickableManager
{
    /**
     * @var Rebrickable
     */
    private $rebrickableClient;

    /**
     * RebrickableManager constructor.
     *
     * @param Rebrickable $rebrickableClient
     */
    public function __construct(Rebrickable $rebrickableClient)
    {
        $this->rebrickableClient = $rebrickableClient;
    }

    public function getSetParts($setNumber)
    {
        return $this->rebrickableClient->getSetParts($setNumber);
    }

    public function getPartById($id)
    {
        return $this->rebrickableClient->getPart($id);
    }
}
