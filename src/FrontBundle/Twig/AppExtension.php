<?php

namespace FrontBundle\Twig;

use AppBundle\Transformer\FormatTransformer;

class AppExtension extends \Twig_Extension
{
    /** @var FormatTransformer */
    private $formatTransformer;

    /** @var string */
    private $webDir;

    /**
     * AppExtension constructor.
     *
     * @param FormatTransformer $formatTransformer
     */
    public function __construct(FormatTransformer $formatTransformer, $webDir)
    {
        $this->formatTransformer = $formatTransformer;
        $this->webDir = $webDir;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('bytesToSize', [$this, 'bytesToSize']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('remoteSize', [$this, 'remoteSize']),
            new \Twig_SimpleFunction('remoteFilename', [$this, 'remoteFilename']),
            new \Twig_SimpleFunction('fileTimestamp', [$this, 'fileTimestamp']),
        ];
    }

    public function bytesToSize($bytes, $precision = 2)
    {
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

    public function fileTimestamp($filePath)
    {
        $changeDate = filemtime($this->webDir.DIRECTORY_SEPARATOR.$filePath);

        return $filePath.'?'.$changeDate;
    }

    public function remoteFilename($url)
    {
        return basename($url);
    }
}
