<?php

namespace AppBundle\Twig;

use AppBundle\Api\Manager\RebrickableManager;

class AppExtension extends \Twig_Extension
{
    /** @var RebrickableManager */
    private $rebrickableAPIManager;

    /**
     * AppExtension constructor.
     *
     * @param $rebrickableAPIManager
     */
    public function __construct($rebrickableAPIManager)
    {
        $this->rebrickableAPIManager = $rebrickableAPIManager;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('partImage', [$this, 'partImage']),
        ];
    }

    public function partImage($number)
    {
        if ($part = $this->rebrickableAPIManager->getPart($number)) {
            return $part->getImgUrl();
        }
    }
}
