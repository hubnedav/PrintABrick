<?php

namespace App\Repository\Search;

use App\Model\ModelSearch;
use Elastica\Query;
use FOS\ElasticaBundle\Repository;

class ModelRepository extends Repository
{
    /**
     * Create search query from ModelSearch entity.
     *
     * @param ModelSearch $modelSearch
     *
     * @return Query
     */
    private function getSearchQuery(ModelSearch $modelSearch): Query
    {
        $boolQuery = new Query\BoolQuery();

        if ($searchQuery = $modelSearch->getQuery()) {
            $query = new Query\MultiMatch();

            $query->setFields(['name', 'id', 'id.ngrams', 'aliases.id', 'keywords.name']);
            $query->setQuery($searchQuery);
            $query->setFuzziness(0.7);
            $query->setMinimumShouldMatch('80%');
            $query->setOperator('and');
        } else {
            $query = new Query\MatchAll();
        }

        $boolQuery->addMust($query);

        if ($modelSearch->getCategory()) {
            $categoryQuery = new Query\Match();
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

    /**
     * Find models by query with highlighted matched values.
     *
     * @param string $queryString
     * @param int    $limit
     *
     * @return mixed
     */
    public function findHighlighted($queryString, $limit = 500)
    {
        $modelSearch = new ModelSearch();
        $modelSearch->setQuery($queryString);

        $query = $this->getSearchQuery($modelSearch);
        $query->setHighlight([
            'pre_tags' => ['<em>'],
            'post_tags' => ['</em>'],
            'fields' => [
                'name' => new \stdClass(),
                'id' => new \stdClass(),
            ],
        ]);

        return $this->findHybrid($query, $limit);
    }
}
