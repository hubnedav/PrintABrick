<?php

namespace AppBundle\Api\Manager;

use AppBundle\Api\Client\Rebrickable\Converter\PropertyNameConverter;
use AppBundle\Api\Client\Rebrickable\Entity\Color;
use AppBundle\Api\Client\Rebrickable\Entity\Part;
use AppBundle\Api\Client\Rebrickable\Entity\PartCategory;
use AppBundle\Api\Client\Rebrickable\Entity\Set;
use AppBundle\Api\Client\Rebrickable\Entity\Theme;
use AppBundle\Api\Client\Rebrickable\RebrickableClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RebrickableManager
{
    const FORMAT = 'json';

    /**
     * @var RebrickableClient
     */
    private $rebrickableClient;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * RebrickableManager constructor.
     *
     * @param RebrickableClient $rebrickableClient
     */
    public function __construct(RebrickableClient $rebrickableClient)
    {
        $this->rebrickableClient = $rebrickableClient;
        $this->serializer = $this->initSerializer();
    }

    private function initSerializer()
    {
        $encoders = [new JsonEncoder()];
        $objectNormalizer = new ObjectNormalizer(null, new PropertyNameConverter());
        $normalizers = [$objectNormalizer, new ArrayDenormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer;
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
        $data = $this->rebrickableClient->call('GET', 'lego/parts/'.$id);

        return $this->serializer->deserialize($data, Part::class, self::FORMAT);
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
        $data = $this->rebrickableClient->call('GET', 'lego/colors/'.$id);

        return $this->serializer->deserialize($data, Color::class, self::FORMAT);
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
        $data = $this->rebrickableClient->call('GET', 'lego/sets/'.$id);

        return $this->serializer->deserialize($data, Set::class, self::FORMAT);
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
        $data = $this->rebrickableClient->call('GET', 'lego/themes/'.$id);

        return $this->serializer->deserialize($data, Theme::class, self::FORMAT);
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
        $data = $this->rebrickableClient->call('GET', 'lego/part_categories/'.$id);

        return $this->serializer->deserialize($data, PartCategory::class, self::FORMAT);
    }

    public function getPartsByLDrawNumber($number)
    {
        $options = [
            'query' => [
                'ldraw_id' => $number,
            ],
        ];

        $response = $this->rebrickableClient->call('GET', 'lego/parts', $options);

        $data = json_decode($response, true)['results'];

        return $this->serializer->denormalize($data, Part::class.'[]', self::FORMAT);
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
        $options = [
            'query' => [
                'page' => $page,
            ],
        ];

        $response = $this->rebrickableClient->call('GET', 'lego/parts/'.$partId.'/colors/'.$colorId.'/sets', $options);
        $data = json_decode($response, true)['results'];

        return $this->serializer->denormalize($data, Set::class.'[]', self::FORMAT);
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
        $options = [
            'query' => [
                'page' => $page,
            ],
        ];

        $response = $this->rebrickableClient->call('GET', 'lego/sets/'.$setId.'/parts', $options);
        $data = json_decode($response, true)['results'];

        return $this->serializer->denormalize($data, Part::class.'[]', self::FORMAT);
    }
}
