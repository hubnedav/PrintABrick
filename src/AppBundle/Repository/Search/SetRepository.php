<?php

namespace AppBundle\Repository\Search;

use AppBundle\Model\SetSearch;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Range;
use FOS\ElasticaBundle\Repository;

/**
 * Class SetRepository.
 */
class SetRepository extends Repository
{
    /**
     * Create search query from SetSearch entity.
     *
     * @param SetSearch $setSearch
     *
     * @return Query
     */
    public function getSearchQuery(SetSearch $setSearch)
    {
        $boolQuery = new BoolQuery();

        if ($searchQuery = $setSearch->getQuery()) {
            $query = new Query\MultiMatch();

            $query->setFields(['name', 'id', 'id.ngrams']);
            $query->setQuery($searchQuery);
            $query->setFuzziness(0.7);
            $query->setMinimumShouldMatch('80%');
            $query->setOperator('and');

            $boolQuery->addMust($query);
        } else {
            $query = new Query\MatchAll();
            $boolQuery->addMust($query);
        }

        if ($setSearch->getTheme()) {
            $themeQuery = new Match();
            $themeQuery->setField('theme.id', $setSearch->getTheme()->getId());
            $boolQuery->addFilter($themeQuery);
        }

        if ($setSearch->getPartCount()) {
            $range = new Range();
            $range->addField('partCount', [
                'gte' => $setSearch->getPartCount()->getFrom(),
                'lte' => $setSearch->getPartCount()->getTo(),
            ]);
            $boolQuery->addFilter($range);
        }

        if ($setSearch->getYear()) {
            $range = new Range();
            $range->addField('year', [
                'gte' => $setSearch->getYear()->getFrom(),
                'lte' => $setSearch->getYear()->getTo(),
            ]);
            $boolQuery->addFilter($range);
        }

        return new Query($boolQuery);
    }

    /**
     * @param SetSearch $setSearch
     * @param int       $limit
     *
     * @return array
     */
    public function search(SetSearch $setSearch, $limit = 500)
    {
        $query = $this->getSearchQuery($setSearch);

        return $this->find($query, $limit);
    }

    /**
     * Find sets by query with highlighted matched values.
     *
     * @param string $query
     * @param int    $limit
     *
     * @return mixed
     */
    public function findHighlighted($query, $limit = 500)
    {
        $setSearch = new SetSearch();
        $setSearch->setQuery($query);

        /** @var Query $query */
        $query = $this->getSearchQuery($setSearch);

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
