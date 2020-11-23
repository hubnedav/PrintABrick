<?php

namespace App\Repository\Search;

use App\Entity\LDraw\ModelType;
use App\Model\ModelSearch;
use Elastica\Query;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Repository;

class ModelRepository extends Repository
{
    /**
     * Create search query from ModelSearch entity.
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

        $categoryQuery = new Query\Prefix();
        $categoryQuery->setParam('name', '~');
        $boolQuery->addMustNot($categoryQuery);

        $modelType = new Query\BoolQuery();
        $modelType->addShould(new Query\Match('type.name', ModelType::PART));
        $modelType->addShould(new Query\Match('type.name', ModelType::SHORTCUT));
        $boolQuery->addMust($modelType);

        if ($modelSearch->getCategory()) {
            $categoryQuery = new Query\Match();
            $categoryQuery->setField('category.id', $modelSearch->getCategory()->getId());
            $boolQuery->addFilter($categoryQuery);
        }

        return new Query($boolQuery);
    }

    public function search(ModelSearch $modelSearch, array $options = []): PaginatorAdapterInterface
    {
        $query = $this->getSearchQuery($modelSearch);

        return $this->createPaginatorAdapter($query, $options);
    }

    /**
     * Find models by query with highlighted matched values.
     *
     * @param string $queryString
     *
     * @return mixed
     */
    public function findHighlighted($queryString, array $options = []): PaginatorAdapterInterface
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

        return $this->createHybridPaginatorAdapter($query, $options);
    }
}
