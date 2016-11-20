<?php

namespace AppBundle\Client\Rebrickable;

use AppBundle\Client\Rebrickable\Entity\Color;
use AppBundle\Client\Rebrickable\Entity\Part;
use AppBundle\Client\Rebrickable\Entity\Set;
use AppBundle\Client\Rebrickable\Converter\PartPropertyNameConverter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Rebrickable
{
    const BASE_URI = 'https://rebrickable.com/api/';
    const FORMAT = 'json';

    private $client;

    private $apiKey;

    /**
     * RebrickableAPI constructor.
     */
    public function __construct($apiKey)
    {
        $this->client = new Client(['base_uri' => self::BASE_URI]);
        $this->apiKey = $apiKey;
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return null|string
     */
    private function call($method, $parameters = [])
    {
        $parameters['query']['key'] = $this->apiKey;
        $parameters['query']['format'] = self::FORMAT;

        try {
            $response = $this->client->request('GET', $method, $parameters);

            if ($response->getStatusCode() === 200) {
                $content = $response->getBody()->getContents();
                if ($content === 'INVALIDKEY') {
                    //TODO
                    throw new LogicException('Invalid API Key');
                } elseif ($content === 'NOSET' || $content === 'NOPART') {
                    return null;
                } else {
                    return $content;
                }
            } else {
                //TODO
                throw new LogicException($response->getStatusCode());
            }
        } catch (ConnectException $e) {
            //TODO
            throw new LogicException($e->getMessage());
        } catch (ClientException $e) {
            //TODO
            throw new LogicException($e->getMessage());
        }
    }

    private function getSerializer()
    {
        $encoders = array(new JsonEncoder());
        $nameConverter = new PartPropertyNameConverter();
        $normalizers = array(new ObjectNormalizer(null,$nameConverter), new ArrayDenormalizer());
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

        $data = $this->call('get_set_parts', $parameters);

        $serializer = $this->getSerializer();
        $partsSTD = json_decode($data, true)[0]['parts'];

        return $data ? $serializer->denormalize($partsSTD, Part::class.'[]', self::FORMAT) : null;
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

        $data = $this->call('get_part', $parameters);
        $serializer = $this->getSerializer();

        return $data ? $serializer->deserialize($data, Part::class, self::FORMAT) : null;
    }

    /**
     * Get associative array of colors used by all parts where key == rb_color_id
     *
     * @return Color[]|null
     */
    public function getColors()
    {
        $data = json_decode($this->call('get_colors'), true);

        $serializer = $this->getSerializer();

        $colors = [];

        foreach ($data as $item) {
            $color = $serializer->denormalize($item, Color::class, self::FORMAT);
            $colors[$color->getRbColorId()] = $color;
        }

        return $data ? $colors : null;
    }

    /**
     * Get associative array of themes used by all parts where key == part_type_id
     *
     * @return string[]
     */
    public function getPartTypes()
    {
        $data = json_decode($this->call('get_part_types'), true)['part_types'];

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

        $data = json_decode($this->call('get_part_sets', $parameters), true)[0]['sets'];

        return $data ? $serializer->denormalize($data, Set::class.'[]', self::FORMAT) : null;
    }
}
