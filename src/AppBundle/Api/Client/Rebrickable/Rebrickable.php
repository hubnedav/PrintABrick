<?php

namespace AppBundle\Api\Client\Rebrickable;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Asset\Exception\LogicException;

class Rebrickable
{
    const BASE_URI = 'https://rebrickable.com/api/';
    const FORMAT = 'json';

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
     * @param $parameters
     *
     * @return null|string
     */
    public function call($method, $parameters = [])
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
                }

                return $content;
            }
                //TODO
                throw new LogicException($response->getStatusCode());
        } catch (ConnectException $e) {
            //TODO
            throw new LogicException($e->getMessage());
        } catch (ClientException $e) {
            //TODO
            throw new LogicException($e->getMessage());
        }
    }
}
