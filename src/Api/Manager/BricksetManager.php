<?php

namespace App\Api\Manager;

use App\Api\Client\Brickset\BricksetClient;
use App\Api\Client\Brickset\Entity\AdditionalImage;
use App\Api\Client\Brickset\Entity\Instructions;
use App\Api\Client\Brickset\Entity\Review;
use App\Api\Client\Brickset\Entity\Set;
use App\Api\Client\Brickset\Entity\Subtheme;
use App\Api\Client\Brickset\Entity\Theme;
use App\Api\Client\Brickset\Entity\Year;
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
        if (!$data = unserialize($this->cache->fetch('themes'), [Theme::class])) {
            $data = $this->bricksetClient->getThemes();
            $this->cache->save('themes', serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSubthemesByTheme($theme)
    {
        $key = 'subthemes-'.$theme;

        if (!$data = unserialize($this->cache->fetch($key), [Subtheme::class])) {
            $data = $this->bricksetClient->getSubthemes($theme);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getYearsByTheme($theme)
    {
        $key = 'years-'.$theme;

        if (!$data = unserialize($this->cache->fetch($key), [Year::class])) {
            $data = $this->bricksetClient->getYears($theme);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetById($id)
    {
        $key = 'set-'.$id;

        if (!$data = unserialize($this->cache->fetch($key), [Set::class])) {
            $data = $this->bricksetClient->getSet($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetByNumber($number)
    {
        $key = 'set-'.$number;

        if (!$data = unserialize($this->cache->fetch($key), [Set::class])) {
            $sets = $this->bricksetClient->getSets(['setNumber' => $number]);
            $data = isset($sets[0]) ? $sets[0] : null;

            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetInstructions($id)
    {
        $key = 'instructions-'.$id;

        if (!$data = unserialize($this->cache->fetch($key), [Instructions::class])) {
            $data = $this->bricksetClient->getInstructions($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getSetReviews($id)
    {
        $key = 'reviews-'.$id;

        if (!$data = unserialize($this->cache->fetch($key), [Review::class])) {
            $data = $this->bricksetClient->getReviews($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }

    public function getAdditionalImages($id)
    {
        $key = 'images-'.$id;

        if (!$data = unserialize($this->cache->fetch($key), [AdditionalImage::class])) {
            $data = $this->bricksetClient->getAdditionalImages($id);
            $this->cache->save($key, serialize($data), self::CACHE_LIFETIME);
        }

        return $data;
    }
}
