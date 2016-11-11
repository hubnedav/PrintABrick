<?php

namespace AppBundle\Service;

use AppBundle\Client\Brickset\Brickset;
use AppBundle\Client\Rebrickable\Rebrickable;

class CollectionService
{

	/**
	 * @var Brickset
	 */
	private $bricksetClient;

	/**
	 * @var Rebrickable
	 */
	private $rebrickableClient;

	/**
	 * CollectionService constructor.
	 *
	 * @param $bricksetClient
	 * @param $rebrickableClient
	 */
	public function __construct($bricksetClient, $rebrickableClient)
	{
		$this->bricksetClient = $bricksetClient;
		$this->rebrickableClient = $rebrickableClient;
	}
}