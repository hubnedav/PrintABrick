<?php

namespace App\Repository\Search;

use App\Model\SetSearch;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Range;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Repository;

/**
 * Class SetRepository.
 */
class SetRepository extends Repository
{
    /**
     * Create search query from SetSearch entity.
     */
    public function getSearchQuery(SetSearch $setSearch): Query
    {
        $boolQuery = new BoolQuery();

        if ($searchQuery = $setSearch->getQuery()) {
            $query = new Query\MultiMatch();

            $query->setFields(['name', 'id', 'id.ngrams', 'theme.name']);
            $query->setQuery($searchQuery);
            $query->setFuzziness(0.7);
            $query->setMinimumShouldMatch('80%');
            $query->setOperator('and');

            $boolQuery->addMust($query);
        } else {
            $query = new Query\MatchAll();
            $boolQuery->addMust($query);
        }

        $boolQuery->addMust(new Match('disabled', false));

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

        if ($setSearch->getCompleteness() ? $setSearch->getCompleteness()->getFrom() > 1 : false) {
            $range = new Range();
            $range->addField('completeness', [
                'gte' => $setSearch->getCompleteness()->getFrom(),
                'lte' => $setSearch->getCompleteness()->getTo(),
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

        $range = new Range();
        $range->addField('partCount', [
            'gte' => 1,
        ]);
        $boolQuery->addFilter($range);

//        $range = new Range();
//        $range->addField('completeness', [
//            'gte' => 1,
//        ]);
//        $boolQuery->addFilter($range);

        return new Query($boolQuery);
    }

    public function search(SetSearch $setSearch): PaginatorAdapterInterface
    {
        $query = $this->getSearchQuery($setSearch);

        return $this->createPaginatorAdapter($query);
    }

    /**
     * Find sets by query with highlighted matched values.
     *
     * @return mixed
     */
    public function findHighlighted(string $query, array $options = []): PaginatorAdapterInterface
    {
        $setSearch = new SetSearch();
        $setSearch->setQuery($query);

        $query = $this->getSearchQuery($setSearch);

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
