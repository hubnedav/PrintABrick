<?php

namespace AppBundle\Service;

use AppBundle\Api\Client\Rebrickable\Entity\Part;
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

    public function getSet($number)
    {
        return $this->em->getRepository('AppBundle:BuildingKit')->findOneBy(['number' => $number]);
    }

    public function getPart($number)
    {
        return $this->em->getRepository('AppBundle:Part')->findOneBy(['number' => $number]);
    }
}
