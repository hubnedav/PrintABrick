<?php

namespace AppBundle\Api\Client\Rebrickable;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Asset\Exception\LogicException;

class Rebrickable_v3
{
    const BASE_URI = 'https://rebrickable.com/api/v3/';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
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
     * @param $uri
     * @param array $options
     *
     * @return string
     */
    public function call($method, $uri, $options = [])
    {
        $options['query']['key'] = $this->apiKey;
        $options['headers'] = [
            'Accept' => 'application/json',
        ];

        try {
            $response = $this->client->request($method, $uri, $options);

            $content = $response->getBody()->getContents();

            return $content;
        } catch (ConnectException $e) {
            //TODO
            throw new LogicException($e);
        } catch (ClientException $e) {
            //TODO
            if ($e->getCode() == 404) {
                throw new LogicException('Not Found');
            }
            if ($e->getCode() == 500) {
                throw new LogicException('Invalid token');
            }
            throw new LogicException($e);
        }
    }
}
