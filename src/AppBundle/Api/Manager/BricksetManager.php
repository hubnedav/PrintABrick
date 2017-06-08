<?php

namespace AppBundle\Api\Manager;

use AppBundle\Api\Client\Brickset\BricksetClient;
use Doctrine\Common\Cache\CacheProvider;

class BricksetManager
{
    /**
     * @var BricksetClient
     */
    private $bricksetClient;

    /**
     * @var CacheProvider
     */
    private $cache;

    const CACHE_LIFETIME = 86400;

    /**
     * BricksetManager constructor.
     *
     * @param BricksetClient $bricksetClient
     * @param CacheProvider  $cache
     */
    public function __construct(BricksetClient $bricksetClient, CacheProvider $cache)
    {
        $this->bricksetClient = $bricksetClient;
        $this->cache = $cache;
    }

    public function getThemes()
    {
        if (!$data = unserialize($this->cache->fetch('themes'))) {
            $data = $this->bricksetClient->getThemes();
            $this->cache->save('themes', serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSubthemesByTheme($theme)
    {
        $key = 'subthemes-'.$theme;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $data = $this->bricksetClient->getSubthemes($theme);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getYearsByTheme($theme)
    {
        $key = 'years-'.$theme;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $data = $this->bricksetClient->getYears($theme);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetById($id)
    {
        $key = 'set-'.$id;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $data = $this->bricksetClient->getSet($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetByNumber($number)
    {
        $key = 'set-'.$number;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $sets = $this->bricksetClient->getSets(['setNumber' => $number]);
            $data = isset($sets[0]) ? $sets[0] : null;

            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetInstructions($id)
    {
        $key = 'instructions-'.$id;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $data = $this->bricksetClient->getInstructions($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetReviews($id)
    {
        $key = 'reviews-'.$id;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $data = $this->bricksetClient->getReviews($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getAdditionalImages($id)
    {
        $key = 'images-'.$id;

        if (!$data = unserialize($this->cache->fetch($key))) {
            $data = $this->bricksetClient->getAdditionalImages($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }
}
