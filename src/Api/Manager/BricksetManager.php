<?php

namespace App\Api\Manager;

use App\Api\Client\Brickset\BricksetClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class BricksetManager
{
    private BricksetClient $bricksetClient;
    private CacheInterface $cache;

    /**
     * BricksetManager constructor.
     */
    public function __construct(BricksetClient $bricksetClient, CacheInterface $bricksetCache)
    {
        $this->bricksetClient = $bricksetClient;
        $this->cache = $bricksetCache;
    }

    public function isEnabled(): bool
    {
        return $this->bricksetClient->getApiKey();
    }

    public function getThemes()
    {
        return $this->cache->get('themes', function (ItemInterface $item) {
            return $this->bricksetClient->getThemes();
        });
    }

    public function getSubthemesByTheme($theme)
    {
        return $this->cache->get("subthemes-{$theme}", function (ItemInterface $item) use ($theme) {
            return $this->bricksetClient->getSubthemes($theme);
        });
    }

    public function getYearsByTheme($theme)
    {
        return $this->cache->get("years-{$theme}", function (ItemInterface $item) use ($theme) {
            return $this->bricksetClient->getYears($theme);
        });
    }

    public function getSetById($id)
    {
        return $this->cache->get("set-{$id}", function (ItemInterface $item) use ($id) {
            return $this->bricksetClient->getSet($id);
        });
    }

    public function getSetByNumber($number)
    {
        return $this->cache->get("set-{$number}", function (ItemInterface $item) use ($number) {
            $sets = $this->bricksetClient->getSets(['setNumber' => $number]);

            return $sets[0] ?? null;
        });
    }

    public function getSetInstructions($id)
    {
        return $this->cache->get("instructions-{$id}", function (ItemInterface $item) use ($id) {
            return $this->bricksetClient->getInstructions($id);
        });
    }

    public function getSetReviews($id)
    {
        return $this->cache->get("reviews-{$id}", function (ItemInterface $item) use ($id) {
            return $this->bricksetClient->getReviews($id);
        });
    }

    public function getAdditionalImages($id)
    {
        return $this->cache->get("images-{$id}", function (ItemInterface $item) use ($id) {
            return $this->bricksetClient->getAdditionalImages($id);
        });
    }
}
