<?php

namespace AppBundle\Twig;

use AppBundle\Api\Manager\RebrickableManager;
use AppBundle\Transformer\FormatTransformer;

class AppExtension extends \Twig_Extension
{
    /** @var RebrickableManager */
    private $rebrickableAPIManager;

    /** @var FormatTransformer */
    private $formatTransformer;

    /**
     * AppExtension constructor.
     *
     * @param RebrickableManager $rebrickableAPIManager
     */
    public function __construct($rebrickableAPIManager, $formatTransformer)
    {
        $this->rebrickableAPIManager = $rebrickableAPIManager;
        $this->formatTransformer = $formatTransformer;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('partImage', [$this, 'partImage']),
            new \Twig_SimpleFilter('setImage', [$this, 'setImage']),
            new \Twig_SimpleFilter('bytesToSize', [$this, 'bytesToSize']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('remoteSize', [$this, 'remoteSize']),
            new \Twig_SimpleFunction('remoteFilename', [$this, 'remoteFilename']),
        ];
    }

    public function partImage($number, $color = null)
    {
        return '/parts/ldraw/'.($color !== null ? $color : '-1').'/'.$number.'.png';
    }

    public function setImage($number)
    {
        return '/sets/'.strtolower($number).'.jpg';
    }

    public function bytesToSize($bytes, $precision = 2) {
        return $this->formatTransformer->bytesToSize($bytes, $precision);
    }

    public function remoteSize($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        return $size;
    }

    public function remoteFilename($url)
    {
        return basename($url);
    }
}
