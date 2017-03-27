<?php

namespace AppBundle\Manager;

class BaseManager
{
    protected $repository;

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
