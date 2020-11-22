<?php

namespace App\Api\Manager;

use App\Api\Client\Rebrickable\Converter\PropertyNameConverter;
use App\Api\Client\Rebrickable\Entity\Color;
use App\Api\Client\Rebrickable\Entity\Part;
use App\Api\Client\Rebrickable\Entity\PartCategory;
use App\Api\Client\Rebrickable\Entity\Set;
use App\Api\Client\Rebrickable\Entity\Theme;
use App\Api\Client\Rebrickable\RebrickableClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RebrickableManager
{
    const FORMAT = 'json';

    private RebrickableClient $rebrickableClient;
    private SerializerInterface $serializer;
    private CacheInterface $cache;

    /**
     * RebrickableManager constructor.
     */
    public function __construct(RebrickableClient $rebrickableClient, CacheInterface $rebrickableCache)
    {
        $this->rebrickableClient = $rebrickableClient;
        $this->serializer = $this->initSerializer();
        $this->cache = $rebrickableCache;
    }

    private function initSerializer()
    {
        $encoders = [new JsonEncoder()];
        $objectNormalizer = new ObjectNormalizer(null, new PropertyNameConverter());
        $normalizers = [$objectNormalizer, new ArrayDenormalizer()];

        return new Serializer($normalizers, $encoders);
    }

    /**
     * Get details about a specific part.
     *
     * @param $id
     *
     * @return Part
     */
    public function getPart($id)
    {
        return $this->cache->get("lego/parts/{$id}", function (ItemInterface $item) use ($id) {
            $data = $this->rebrickableClient->call('GET', "lego/parts/{$id}");

            return $this->serializer->deserialize($data, Part::class, self::FORMAT);
        });
    }

    /**
     * Get details about a specific Color.
     *
     * @param $id
     *
     * @return Color
     */
    public function getColor($id)
    {
        return $this->cache->get("lego/colors/{$id}", function (ItemInterface $item) use ($id) {
            $data = $this->rebrickableClient->call('GET', "lego/colors/{$id}");

            return $this->serializer->deserialize($data, Color::class, self::FORMAT);
        });
    }

    /**
     * Get details for a specific Set.
     *
     * @param $id
     *
     * @return Set
     */
    public function getSet($id)
    {
        return $this->cache->get("lego/sets/{$id}", function (ItemInterface $item) use ($id) {
            $data = $this->rebrickableClient->call('GET', "lego/sets/{$id}");

            return $this->serializer->deserialize($data, Set::class, self::FORMAT);
        });
    }

    /**
     * Return details for a specific Theme.
     *
     * @param $id
     *
     * @return Theme
     */
    public function getTheme($id)
    {
        return $this->cache->get("lego/themes/{$id}", function (ItemInterface $item) use ($id) {
            $data = $this->rebrickableClient->call('GET', "lego/themes/{$id}");

            return $this->serializer->deserialize($data, Theme::class, self::FORMAT);
        });
    }

    /**
     * Return details for a specific PartCategory.
     *
     * @param $id
     *
     * @return PartCategory
     */
    public function getPartCategory($id)
    {
        return $this->cache->get("lego/part_categories/{$id}", function (ItemInterface $item) use ($id) {
            $data = $this->rebrickableClient->call('GET', 'lego/part_categories/'.$id);

            return $this->serializer->deserialize($data, PartCategory::class, self::FORMAT);
        });
    }

    public function getPartsByLDrawNumber($number)
    {
        return $this->cache->get('lego/parts-'.$number, function (ItemInterface $item) use ($number) {
            $options = [
                'query' => [
                    'ldraw_id' => $number,
                ],
            ];

            $response = $this->rebrickableClient->call('GET', 'lego/parts', $options);

            $data = json_decode($response, true)['results'];

            return $this->serializer->denormalize($data, Part::class.'[]', self::FORMAT);
        });
    }

    /**
     * Get the list of sets that a specific part/color appears in.
     *
     * @param $partId
     * @param $colorId
     * @param $page
     *
     * @return Set[]
     */
    public function getPartSets($partId, $colorId, $page = null)
    {
        return $this->cache->get("lego/parts/{$partId}/colors/{$colorId}/sets", function (ItemInterface $item) use ($partId, $colorId, $page) {
            $options = [
                'query' => [
                    'page' => $page,
                ],
            ];

            $response = $this->rebrickableClient->call(
                'GET',
                "lego/parts/{$partId}/colors/{$colorId}/sets",
                $options
            );
            $data = json_decode($response, true)['results'];

            return $this->serializer->denormalize($data, Set::class.'[]', self::FORMAT);
        });
    }

    /**
     * Get a list of all parts (normal + spare) used in a set.
     *
     * @param $setId
     * @param $page
     *
     * @return Part[]
     */
    public function getSetParts($setId, $page = null)
    {
        return $this->cache->get("lego/sets/{$setId}/parts", function (ItemInterface $item) use ($setId, $page) {
            $options = [
                'query' => [
                    'page' => $page,
                ],
            ];

            $response = $this->rebrickableClient->call('GET', "lego/sets/{$setId}/parts", $options);
            $data = json_decode($response, true)['results'];

            return $this->serializer->denormalize($data, Part::class.'[]', self::FORMAT);
        });
    }
}
