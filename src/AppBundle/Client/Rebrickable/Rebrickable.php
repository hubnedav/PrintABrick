<?php

namespace AppBundle\Client\Rebrickable;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Asset\Exception\LogicException;

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


	private function call($method, $parameters) {
		$parameters['query']['key'] = $this->apiKey;
		$parameters['query']['format'] = self::FORMAT;

		try
		{
			$response = $this->client->request('GET', $method, $parameters);

			if ($response->getStatusCode() === 200)
			{
				$content = $response->getBody()->getContents();
				if($content === 'INVALIDKEY') {
					//TODO
					throw new LogicException("Invalid API Key");
				} else {
					return $content;
				}
			}
			else
			{
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

	/**
	 * Get a list of all parts (normal + spare) used in a set.
	 *
	 * @param string $setName unique rebrickable set name
	 *
	 * @return array
	 */
	public function getSetParts($setName)
	{
		$parameters = [
			'query' => [
				'set' => $setName,
			]
		];

		return $this->call('get_set_parts', $parameters);
	}

	/**
	 * Get details about a specific part.
	 *
	 * @param $partID
	 *
	 * @return
	 */
	public function getPart($partID)
	{
		$parameters = [
			'query' => [
				'part_id' => $partID,
				'inc_ext' => 1
			]
		];

		return $this->call('get_part',$parameters);
	}

	/**
	 * Get the list of colors used by all parts.
	 *
	 * @return
	 */
	public function getColors()
	{
		return $this->call('get_colors', []);
	}

	/**
	 * Get the list of sets that a specific part/color appears in.
	 *
	 * @param $partID
	 * @param $colorID
	 *
	 * @return
	 */
	public function getPartSets($partID, $colorID)
	{
		$parameters = [
			'query' => [
				'part_id' => $partID,
				'color_id' => $colorID
			]
		];

		return $this->call('get_part_sets', $parameters);
	}
}
