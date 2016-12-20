<?php

namespace AppBundle\Api\Manager;

use AppBundle\Api\Client\Brickset\Brickset;

class BricksetManager
{
    /**
     * @var Brickset
     */
    private $bricksetClient;

    /**
     * BricksetManager constructor.
     *
     * @param Brickset $bricksetClient
     */
    public function __construct(Brickset $bricksetClient)
    {
        $this->bricksetClient = $bricksetClient;
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
}
