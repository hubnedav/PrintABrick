<?php

namespace AppBundle\Service;

use AppBundle\Api\Client\Rebrickable\Rebrickable;
use AppBundle\Api\Manager\BricksetManager;
use Doctrine\ORM\EntityManager;

class CollectionService
{
    /**
     * @var BricksetManager
     */
    protected $bricksetManager;

    /**
     * @var Rebrickable client
     */
    protected $rebrickableManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * CollectionService constructor.
     *
     * @param $em
     * @param $bricksetManager
     * @param $rebrickableManager
     */
    public function __construct($em, $bricksetManager, $rebrickableManager)
    {
        $this->em = $em;
        $this->bricksetManager = $bricksetManager;
        $this->rebrickableManager = $rebrickableManager;
    }
}
