<?php

namespace App\Service;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Set;
use App\Model\ModelSearch;
use App\Model\SetSearch;
use App\Repository\Search\ModelRepository;
use App\Repository\Search\SetRepository;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;

class SearchService
{
    private ModelRepository $modelRepository;
    private SetRepository $setRepository;

    /**
     * SearchService constructor.
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
     */
    public function searchSetsByQuery(string $query): PaginatorAdapterInterface
    {
        return $this->setRepository->search(new SetSearch($query));
    }

    /**
     * Find matching sets by query with highlights.
     *
     * @param $query
     */
    public function searchSetsHighlightedByQuery(string $query, array $options = []): PaginatorAdapterInterface
    {
        return $this->setRepository->findHighlighted($query, $options);
    }

    /**
     * Find matching sets by rules in SetSearch class.
     */
    public function searchSets(SetSearch $setSearch): PaginatorAdapterInterface
    {
        return $this->setRepository->search($setSearch);
    }

    /**
     * Find matching models by query.
     *
     * @param $query
     */
    public function searchModelsByQuery($query, array $options = []): PaginatorAdapterInterface
    {
        return $this->modelRepository->search(new ModelSearch($query), $options);
    }

    /**
     * Find matching models by query with highlights.
     *
     * @param $query
     */
    public function searchModelsHighlightedByQuery($query, array $options = []): PaginatorAdapterInterface
    {
        return $this->modelRepository->findHighlighted($query, $options);
    }

    /**
     * Find matching models by rules in ModelSearch class.
     */
    public function searchModels(ModelSearch $modelSearch, array $options = []): PaginatorAdapterInterface
    {
        return $this->modelRepository->search($modelSearch, $options);
    }
}
