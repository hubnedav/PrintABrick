<?php

namespace App\Api\Client\Rebrickable;

use App\Api\Exception\ApiException;
use App\Api\Exception\AuthenticationFailedException;
use App\Api\Exception\CallFailedException;
use App\Api\Exception\EmptyResponseException;
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
     * @param string $rebrickableApiKey
     */
    public function __construct(string $rebrickableApiKey)
    {
        $this->client = new Client(['base_uri' => self::BASE_URI]);
        $this->apiKey = $rebrickableApiKey;
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
            if (404 == $e->getCode()) {
                throw new EmptyResponseException(ApiException::REBRICKABLE);
            } elseif (401 == $e->getCode()) {
                throw new AuthenticationFailedException(ApiException::REBRICKABLE);
            }

            throw new ApiException(ApiException::REBRICKABLE, $e, $e->getCode());
        }
    }
}
