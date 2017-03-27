<?php

namespace AppBundle\Manager\LDraw;

use AppBundle\Entity\LDraw\Keyword;
use AppBundle\Manager\BaseManager;
use AppBundle\Repository\LDraw\KeywordRepository;

class KeywordManager extends BaseManager
{
    /**
     * KeywordManager constructor.
     *
     * @param KeywordRepository $repository
     */
    public function __construct(KeywordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new Keyword entity with $name or retrieve one.
     *
     * @param $name
     *
     * @return Keyword
     */
    public function create($name)
    {
        if (($keyword = $this->repository->findByName($name)) == null) {
            $keyword = new Keyword();
            $keyword->setName($name);
        }

        return $keyword;
    }
}
