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

    public function getSetByNumber($number)
    {
        $sets = $this->bricksetClient->getSets(['setNumber' => $number]);

        return isset($sets[0]) ? $sets[0] : null;
    }

    public function getSetInstructions($id) {
        return $this->bricksetClient->getInstructions($id);
    }

    public function getSetReviews($id) {
        return $this->bricksetClient->getReviews($id);
    }

    public function getAdditionalImages($id) {
        return $this->bricksetClient->getAdditionalImages($id);
    }
}
