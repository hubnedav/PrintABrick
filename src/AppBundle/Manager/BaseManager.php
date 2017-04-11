<?php

namespace AppBundle\Manager;

use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\EntityManager;

class BaseManager
{
    /** @var EntityManager $em */
    protected $em;

    protected $repository;

    /**
     * @return BaseRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    public function setEntityManager(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
}
