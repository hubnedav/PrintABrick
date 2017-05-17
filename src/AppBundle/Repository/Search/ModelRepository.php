<?php

namespace AppBundle\Repository\Search;

use AppBundle\Model\ModelSearch;
use FOS\ElasticaBundle\Repository;

class ModelRepository extends Repository
{
    public function getSearchQuery(ModelSearch $modelSearch) {
        $boolQuery = new \Elastica\Query\BoolQuery();

        if ($searchQuery = $modelSearch->getQuery()) {
            $query = new \Elastica\Query\MultiMatch();

            $query->setFields(['name', 'id', 'aliases.id', 'keywords.name']);
            $query->setQuery($searchQuery);
            $query->setFuzziness(0.7);
            $query->setMinimumShouldMatch('80%');
        } else {
            $query = new \Elastica\Query\MatchAll();
        }

        $boolQuery->addMust($query);

        if ($modelSearch->getCategory()) {
            $categoryQuery = new \Elastica\Query\Match();
            $categoryQuery->setField('category.id', $modelSearch->getCategory()->getId());
            $boolQuery->addFilter($categoryQuery);
        }

        return $boolQuery;
    }

    public function search(ModelSearch $modelSearch)
    {
       $query = $this->getSearchQuery($modelSearch);
        return $this->find($query, 500);
    }
}
