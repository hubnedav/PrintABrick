<?php

namespace AppBundle\Api\Manager;

use AppBundle\Api\Client\Rebrickable\Converter\PartPropertyNameConverter;
use AppBundle\Api\Client\Rebrickable\Entity\Color;
use AppBundle\Api\Client\Rebrickable\Entity\Part;
use AppBundle\Api\Client\Rebrickable\Entity\Set;
use AppBundle\Api\Client\Rebrickable\Rebrickable;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RebrickableManager
{
    const FORMAT = 'json';

    /**
     * @var Rebrickable
     */
    private $rebrickableClient;

    /**
     * RebrickableManager constructor.
     *
     * @param Rebrickable $rebrickableClient
     */
    public function __construct(Rebrickable $rebrickableClient)
    {
        $this->rebrickableClient = $rebrickableClient;
    }

    private function getSerializer()
    {
        $encoders = [new JsonEncoder()];
        $nameConverter = new PartPropertyNameConverter();
        $objectNormalizer = new ObjectNormalizer(null, $nameConverter);
        $normalizers = [$objectNormalizer, new ArrayDenormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer;
    }

    /**
     * Get a list of all parts (normal + spare) used in a set.
     *
     * @param string $setName unique rebrickable set name
     *
     * @return Part[]|null
     */
    public function getSetParts($setName)
    {
        $parameters = [
            'query' => [
                'set' => $setName,
            ],
        ];

        $data = $this->rebrickableClient->call('get_set_parts', $parameters);

        $serializer = $this->getSerializer();
        $partsSTD = json_decode($data, true)[0]['parts'];

        if ($data) {
            $parts = $serializer->denormalize($partsSTD, Part::class.'[]', self::FORMAT);
            foreach ($parts as $key => &$part) {
                $part->setCategory($this->getPartTypes()[$partsSTD[$key]['part_type_id']]);
                $part->setColors([
                    0 => [
                        'color_name' => $partsSTD[$key]['color_name'],
                        'rb_color_id' => $partsSTD[$key]['rb_color_id'],
                        'ldraw_color_id' => $partsSTD[$key]['ldraw_color_id'],
                    ],
                ]);
            }

            return $data;
        }

        return null;
    }

    /**
     * Get details about a specific part.
     *
     * @param $partID
     *
     * @return Part|null
     */
    public function getPart($partID)
    {
        $parameters = [
            'query' => [
                'part_id' => $partID,
                'inc_ext' => 1,
            ],
        ];

        $data = $this->rebrickableClient->call('get_part', $parameters);
        $serializer = $this->getSerializer();

        return $data ? $serializer->deserialize($data, Part::class, self::FORMAT) : null;
    }

    /**
     * Get associative array of colors used by all parts where key == rb_color_id.
     *
     * @return Color[]|null
     */
    public function getColors()
    {
        $data = json_decode($this->rebrickableClient->call('get_colors'), true);

        $serializer = $this->getSerializer();

        $colors = [];

        foreach ($data as $item) {
            $color = $serializer->denormalize($item, Color::class, self::FORMAT);
            $colors[$color->getRbColorId()] = $color;
        }

        return $data ? $colors : [];
    }

    /**
     * Get associative array of themes used by all parts where key == part_type_id.
     *
     * @return string[]
     */
    public function getPartTypes()
    {
        $data = json_decode($this->rebrickableClient->call('get_part_types'), true)['part_types'];

        $types = [];
        foreach ($data as $item) {
            $types[$item['part_type_id']] = $item['desc'];
        }

        return $data ? $types : null;
    }

    /**
     * Get the list of sets that a specific part/color appears in.
     *
     * @param $partID
     * @param $colorID
     *
     * @return Set[]
     */
    public function getPartSets($partID, $colorID)
    {
        $parameters = [
            'query' => [
                'part_id' => $partID,
                'color_id' => $colorID,
            ],
        ];

        $serializer = $this->getSerializer();

        $data = json_decode($this->rebrickableClient->call('get_part_sets', $parameters), true)[0]['sets'];

        return $data ? $serializer->denormalize($data, Set::class.'[]', self::FORMAT) : null;
    }
}
