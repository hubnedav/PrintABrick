<?php

namespace AppBundle\Repository\Search;

use AppBundle\Model\ModelSearch;
use Elastica\Query;
use FOS\ElasticaBundle\Repository;

class ModelRepository extends Repository
{
    /**
     * @param ModelSearch $modelSearch
     * @return \Elastica\Query
     */
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

        return new Query($boolQuery);
    }

    public function search(ModelSearch $modelSearch, $limit = 500)
    {
       $query = $this->getSearchQuery($modelSearch);
        return $this->find($query, $limit);
    }

    public function findHighlighted($query, $limit = 500) {
        $modelSearch = new ModelSearch();
        $modelSearch->setQuery($query);

        /** @var Query $query */
        $query = $this->getSearchQuery($modelSearch);

        $query->setHighlight([
            'pre_tags' => ['<em>'],
            'post_tags' => ['</em>'],
            "fields" => [
                "name" => new \stdClass(),
                "id" => new \stdClass()
            ]
        ]);

        return $this->findHybrid($query, $limit);
    }
}
