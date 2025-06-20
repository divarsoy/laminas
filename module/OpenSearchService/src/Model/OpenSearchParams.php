<?php
namespace OpenSearchService\Model;

/**
 * Encapsulates parameters for OpenSearch queries.
 */
class OpenSearchParams
{
    private string $index;
    private int $size = 20;
    private array $baseFilter = [];
    private array $baseMust = [];
    private array $aggs = [];
    private array $dateRangeAggs = [];
    private array $singleDateMust = [];
    private array $dateRangeMust = [];
    private array $availabilityMust = [];

    public function __construct(string $indexName)
    {
        $this->index = $indexName;
        $this->aggs = $this->defaultAggs();
    }

    /**
     * Returns the default aggregations.
     */
    private function defaultAggs(): array
    {
        return [
            'apartment_type' => ['terms' => ['field' => 'apartment_type']],
            'cities' => ['terms' => ['field' => 'city']],
            'building_type' => ['terms' => ['field' => 'building_type']],
            'apartment_facilities' => ['terms' => ['field' => 'apartment_facilities']],
            'kitchen_facilities' => ['terms' => ['field' => 'kitchen_facilities']],
            'building_facilities' => ['terms' => ['field' => 'building_facilities']],
            'health_and_safety_facilities' => ['terms' => ['field' => 'health_and_safety_facilities']],
            'sustainability' => ['terms' => ['field' => 'sustainability']],
            'rate_ranges' => [
                'range' => [
                    'field' => 'rate',
                    'ranges' => [
                        ['to' => 100],
                        ['from' => 100, 'to' => 200],
                        ['from' => 200]
                    ]
                ]
            ]
        ];
    }

    /**
     * Add a base filter
     */
    public function addBaseFilter(array $filter): void
    {
        $this->baseFilter[] = $filter;
    }

    /**
     * Add a base must clause
     */
    public function addBaseMust(array $must): void
    {
        $this->baseMust[] = $must;
    }

    /**
     * Set the aggregations for the query.
     */
    public function setAggs(array $aggs): void
    {
        $this->aggs = $aggs;
    }

    /**
     * Set the aggregations for date range queries.
     */
    public function setDateRangeAggs(array $aggs): void
    {
        $this->dateRangeAggs = $aggs;
    }

    /**
     * Add a must clause for date range queries.
     */
    public function addDateRangeMust(array $must): void
    {
        $this->dateRangeMust[] = $must;
    }

    /**
     * Set the size of the result set.
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Add a must clause for a single date.
     */
    public function setSingleDateMust(array $must): void
    {
        $this->singleDateMust[] = $must;
    }

    /**
     * Add a must clause for availability.
     */
    public function setAvailabilityMust(array $must): void
    {
        $this->availabilityMust[] = $must;
    }

    /**
     * Build parameters for queries without a date range.
     */
    public function buildNoDateParams(): array
    {
        return [
            'index' => $this->index,
            'body' => [
                'size' => $this->size,
                'query' => [
                    'bool' => [
                        'must' => array_merge($this->baseMust, $this->singleDateMust),
                        'filter' => $this->baseFilter,
                    ],
                ],
                'aggs' => $this->aggs,
            ],
        ];
    }

    /**
     * Build parameters for queries with a date range.
     */
    public function buildDateRangeParams(): array
    {
        return [
            'index' => $this->index,
            'body' => [
                'size' => $this->size,
                'query' => [
                    'bool' => [
                        'must' => array_merge($this->baseMust, $this->availabilityMust, $this->dateRangeMust),
                        'filter' => $this->baseFilter,
                    ],
                ],
                'aggs' => $this->dateRangeAggs,
            ],
        ];
    }

    /**
     * Build parameters for filtered date range queries.
     */
    public function buildFilteredDateRangeParams(array $filter): array
    {
        return [
            'index' => $this->index,
            'body' => [
                'size' => $this->size,
                'query' => [
                    'bool' => [
                        'must' => array_merge($this->baseMust, $this->availabilityMust, $this->singleDateMust),
                        'filter' => array_merge($this->baseFilter, [$filter]),
                    ],
                ],
                'aggs' => $this->aggs,
            ],
        ];
    }

    /**
     * Reset all stateful arrays for reuse.
     */
    public function reset(): void
    {
        $this->baseFilter = [];
        $this->baseMust = [];
        $this->dateRangeMust = [];
        $this->availabilityMust = [];
        $this->singleDateMust = [];
        $this->dateRangeAggs = [];
    }
}