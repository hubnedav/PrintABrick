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

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('remoteSize', [$this, 'remoteSize']),
            new \Twig_SimpleFunction('remoteFilename', [$this, 'remoteFilename']),
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

    public function remoteSize($url) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);
        return $size;
    }

    public function remoteFilename($url) {
       return basename($url);
    }
}
