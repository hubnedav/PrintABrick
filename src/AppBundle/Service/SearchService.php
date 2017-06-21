<?php

namespace AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Model\ModelSearch;
use AppBundle\Model\SetSearch;
use AppBundle\Repository\Search\ModelRepository;
use AppBundle\Repository\Search\SetRepository;
use FOS\ElasticaBundle\HybridResult;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;

class SearchService
{
    /** @var ModelRepository */
    private $modelRepository;

    /** @var SetRepository */
    private $setRepository;

    /**
     * SearchService constructor.
     *
     * @param RepositoryManagerInterface $repositoryManager
     */
    public function __construct(RepositoryManagerInterface $repositoryManager)
    {
        $this->modelRepository = $repositoryManager->getRepository(Model::class);
        $this->setRepository = $repositoryManager->getRepository(Set::class);
    }

    /**
     * Find matching sets by query.
     *
     * @param $query
     * @param int $limit
     *
     * @return array
     */
    public function searchSetsByQuery($query, $limit = 1000)
    {
        return $this->setRepository->search(new SetSearch($query), $limit);
    }

    /**
     * Find matching sets by query with highlights.
     *
     * @param $query
     * @param int $limit
     *
     * @return HybridResult[]
     */
    public function searchSetsHighlightedByQuery($query, $limit = 4)
    {
        return $this->setRepository->findHighlighted($query, $limit);
    }

    /**
     * Find matching sets by rules in SetSearch class.
     *
     * @param SetSearch $setSearch
     * @param int       $limit
     *
     * @return array
     */
    public function searchSets(SetSearch $setSearch, $limit = 1000)
    {
        return $this->setRepository->search($setSearch, $limit);
    }

    /**
     * Find matching models by query.
     *
     * @param $query
     * @param int $limit
     *
     * @return array
     */
    public function searchModelsByQuery($query, $limit = 1000)
    {
        return $this->modelRepository->search(new ModelSearch($query), $limit);
    }

    /**
     * Find matching models by query with highlights.
     *
     * @param $query
     * @param int $limit
     *
     * @return HybridResult[]
     */
    public function searchModelsHighlightedByQuery($query, $limit = 4)
    {
        return $this->modelRepository->findHighlighted($query, $limit);
    }

    /**
     * Find matching models by rules in ModelSearch class.
     *
     * @param ModelSearch $modelSearch
     * @param int         $limit
     *
     * @return array
     */
    public function searchModels(ModelSearch $modelSearch, $limit = 1000)
    {
        return $this->modelRepository->search($modelSearch, $limit);
    }
}
