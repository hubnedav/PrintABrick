<?php

namespace FrontBundle\Imagine;

use Liip\ImagineBundle\Binary\Loader\LoaderInterface;

abstract class BaseImageLoader implements LoaderInterface
{
    abstract public function find($path);

    /**
     * Check if file on remote url exists.
     *
     * @param string $url
     *
     * @return bool
     */
    protected function remoteFileExists($url)
    {
        $resource = curl_init($url);
        curl_setopt($resource, CURLOPT_NOBODY, true);
        curl_exec($resource);
        $status = curl_getinfo($resource, CURLINFO_HTTP_CODE);
        curl_close($resource);

        return $status === 200 ? true : false;
    }
}
