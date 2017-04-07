<?php

namespace AppBundle\Twig;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Entity\Rebrickable\Color;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;

class AppExtension extends \Twig_Extension
{
    /** @var RebrickableManager */
    private $rebrickableAPIManager;

    /**
     * AppExtension constructor.
     *
     * @param RebrickableManager $rebrickableAPIManager
     */
    public function __construct($rebrickableAPIManager)
    {
        $this->rebrickableAPIManager = $rebrickableAPIManager;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('partImage', [$this, 'partImage']),
            new \Twig_SimpleFilter('setImage', [$this, 'setImage']),
        ];
    }

    public function partImage(Part $part, Color $color = null)
    {
        return '/parts/ldraw/'.($color ? $color->getId():'-1').'/'.$part->getNumber().'.png';
    }

    public function setImage(Set $set)
    {
        return '/sets/'.strtolower($set->getNumber()).'.jpg';
    }
}
