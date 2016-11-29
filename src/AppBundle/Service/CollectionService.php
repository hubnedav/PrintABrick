<?php

namespace AppBundle\Service;

use AppBundle\Client\Brickset\Brickset;
use AppBundle\Client\Rebrickable\Rebrickable;

class CollectionService
{
    /**
     * @var Brickset client
     */
    protected $bricksetClient;

    /**
     * @var Rebrickable client
     */
    protected $rebrickableClient;

    /**
     * CollectionService constructor.
     *
     * @param $bricksetClient
     * @param $rebrickableClient
     */
    public function __construct($bricksetClient, $rebrickableClient)
    {
        $this->bricksetClient = $bricksetClient;
        $this->rebrickableClient = $rebrickableClient;
    }

    public function getThemes()
    {
        return $this->bricksetClient->getThemes();
    }

    public function getSubthemesByTheme($theme)
    {
        return $this->bricksetClient->getSubthemes($theme);
    }

    public function getYearsByTheme($theme)
    {
        return $this->bricksetClient->getYears($theme);
    }

    public function getSetById($id)
    {
        return $this->bricksetClient->getSet($id);
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
