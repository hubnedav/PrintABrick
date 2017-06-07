<?php

namespace AppBundle\Api\Client\Rebrickable;

use AppBundle\Api\Exception\ApiException;
use AppBundle\Api\Exception\AuthenticationFailedException;
use AppBundle\Api\Exception\CallFailedException;
use AppBundle\Api\Exception\EmptyResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class RebrickableClient
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
     *
     * @param string $apiKey
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
     * @throws ApiException
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
            throw new CallFailedException(ApiException::REBRICKABLE);
        } catch (ClientException $e) {
            if ($e->getCode() == 404) {
                throw new EmptyResponseException(ApiException::REBRICKABLE);
            } elseif ($e->getCode() == 401) {
                throw new AuthenticationFailedException(ApiException::REBRICKABLE);
            }

            throw new ApiException(ApiException::REBRICKABLE, $e, $e->getCode());
        }
    }
}
