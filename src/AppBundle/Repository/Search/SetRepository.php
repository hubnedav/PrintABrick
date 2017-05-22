<?php

namespace AppBundle\Repository\Search;

use AppBundle\Model\SetSearch;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\MultiMatch;
use Elastica\Query\Range;
use Elastica\Query\Wildcard;
use FOS\ElasticaBundle\Repository;

class SetRepository extends Repository
{
    /**
     * @param SetSearch $setSearch
     * @return Query
     */
    public function getSearchQuery(SetSearch $setSearch) {
        $boolQuery = new BoolQuery();

        if ($searchQuery = $setSearch->getQuery()) {
            $query = new Query\MultiMatch();

            $query->setFields(['name', 'id']);
            $query->setQuery($searchQuery);
            $query->setFuzziness(0.7);
            $query->setMinimumShouldMatch('80%');

            $boolQuery->addMust($query);

        } else {
            $query = new \Elastica\Query\MatchAll();
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

    public function search(SetSearch $setSearch, $limit = 500)
    {
        $query = $this->getSearchQuery($setSearch);

        return $this->find($query, $limit);
    }

    public function findHighlighted($query, $limit = null) {
        $setSearch = new SetSearch();
        $setSearch->setQuery($query);

        /** @var Query $query */
        $query = $this->getSearchQuery($setSearch);

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
