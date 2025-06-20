<?php
namespace OpenSearchService\Service;

use OpenSearch\ClientBuilder;
use Carbon\Carbon;
use OpenSearchService\Model\OpenSearchParams;
use OpenSearch\Client;

/**
 * Service for searching properties in OpenSearch.
 */
class OpenSearchService
{
    private Client $client;
    private OpenSearchParams $params;
    private int $numberOfDays = 1;
    private ?Carbon $startDate = null;
    private ?Carbon $endDate = null;
    private bool $ignoreAvailability = false;

    /**
     * @param OpenSearchParams $params
     * @param array $hosts
     */
    public function __construct(OpenSearchParams $params, array $hosts = ['opensearch:9200'])
    {
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
        $this->params = $params;
    }

    /**
     * Main search entry point.
     * @param array $queryParams
     * @return array
     */
    public function search(array $queryParams): array
    {
        $searchText = $queryParams['q'] ?? null;
        $filters = $queryParams['filters'] ?? [];
        $rateMin = $queryParams['rate_min'] ?? null;
        $rateMax = $queryParams['rate_max'] ?? null;
        $startDate = $queryParams['start_date'] ?? null;
        $endDate = $queryParams['end_date'] ?? null;
        $this->initialise();
        $this->parseFacetFilters($filters);
        $this->parseDateFilter($startDate, $endDate);
        $this->parseSearchString($searchText);
        $this->parseRateRange($rateMin, $rateMax);

        if ($this->ignoreAvailability) {
            $results = $this->client->search($this->params->buildNoDateParams());
            return $this->formatResults($results);
        }

        $availableApartments = $this->findAvailableApartments();
        if (empty($availableApartments)) {
            return [
                'results' => [],
                'facets' => [],
                'total' => 0
            ];
        }
        if ($this->startDate === null) {
            throw new \RuntimeException('Start date must be set before searching for apartments by date.');
        }
        $filter = ['terms' => ['apartment_id' => $availableApartments]];
        $params = $this->params->buildFilteredDateRangeParams($filter);
        $results = $this->client->search($params);
        return $this->formatResults($results);
    }

    private function initialise():void{
        $this->params->reset();
        $this->ignoreAvailability = false;
        $this->numberOfDays = 1;
        $this->startDate = null;
        $this->endDate = null;
    }

    private function formatResults(array $results): array
    {
        return [
            'results' => $results['hits']['hits'] ?? [],
            'facets' => $results['aggregations'] ?? [],
            'total' => $results['hits']['total']['value'] ?? 0
        ];
    }

    private function findAvailableApartments(): array
    {
        $this->params->setDateRangeAggs([
            'available_rooms' => [
                'terms' => [
                    'field' => 'apartment_id',
                    'size' => 10000
                ],
                'aggs' => [
                    'dates_available' => [
                        'value_count' => [
                            'field' => 'date'
                        ]
                    ],
                    'only_fully_available' => [
                        'bucket_selector' => [
                            'buckets_path' => [
                                'dateCount' => 'dates_available'
                            ],
                            'script' => "params.dateCount == {$this->numberOfDays}"
                        ]
                    ]
                ],
            ]
        ]);
        $params = $this->params->buildDateRangeParams();
        $availableApartments = $this->client->search($params);
        $apartments = [];
        if (isset($availableApartments['aggregations']['available_rooms']['buckets'])) {
            foreach ($availableApartments['aggregations']['available_rooms']['buckets'] as $bucket) {
                $apartments[] = $bucket['key'];
            }
        }
        return $apartments;
    }
    private function parseRateRange($rateMin, $rateMax): void
    {
        if ($rateMin !== null || $rateMax !== null) {
            $range = [];
            if ($rateMin !== null) {
                $range['gte'] = (float)$rateMin;
            }
            if ($rateMax !== null) {
                $range['lte'] = (float)$rateMax;
            }
            $this->params->addBaseFilter(['range' => ['rate' => $range]]);
        }
    }
    private function parseSearchString(?string $searchText): void
    {
        if ($searchText) {
            $this->params->addBaseMust([
                'bool' => [
                    'should' => [
                        [
                            'multi_match' => [
                                'query' => $searchText,
                                'fields' => ['description','name^2', 'city^3', 'area^4'],
                                'type' => 'best_fields',
                                'operator' => 'and'
                            ]
                        ],
                        [
                            'match_phrase_prefix' => [
                                'city' => [
                                    'query' => $searchText,
                                    'boost' => 3
                                ]
                            ]
                        ],
                        [
                            'match_phrase_prefix' => [
                                'area' => [
                                    'query' => $searchText,
                                    'boost' => 2
                                ]
                            ]
                        ]
                    ],
                    'minimum_should_match' => 1
                ]
            ]);
        }
    }
    private function parseDateFilter($start, $end): void
    {
        try {
            if ($start === null) {
                $this->ignoreAvailability = true;
                $this->startDate = Carbon::now();
            } else {
                $this->startDate = new Carbon($start);
                if (!$this->startDate->isValid()) {
                    throw new \InvalidArgumentException('Invalid start date format. Expected format: YYYY-MM-DD');
                }
            }
            $this->params->setSingleDateMust(['term' => ['date' => $this->startDate->toDateString()]]);
            
            if ($end === null) {
                $this->endDate = Carbon::now()->addDay();
            } else {
                $this->endDate = new Carbon($end);
                if (!$this->endDate->isValid()) {
                    throw new \InvalidArgumentException('Invalid end date format. Expected format: YYYY-MM-DD');
                }
                if ($this->endDate->lt($this->startDate)) {
                    throw new \InvalidArgumentException('End date must be after start date');
                }
                $this->numberOfDays = $this->startDate->diffInDays($this->endDate);
                if ($this->numberOfDays < 1) {
                    throw new \InvalidArgumentException('Date range must be at least 1 day');
                }
                if (!$this->ignoreAvailability) {
                    $this->params->addBaseMust([
                        'term' => ['available' => true]
                    ]);
                    $this->params->addDateRangeMust([
                        'range' => [
                            'date' => [
                                'gte' => $this->startDate->toDateString(),
                                'lt' => $this->endDate->toDateString()
                            ]
                        ]
                    ]);
                }
            }
        } catch (\Exception $e) {
            error_log("Date validation error: " . $e->getMessage());
            throw $e;
        }
    }
    private function parseFacetFilters(array $filters): void
    {
        foreach ($filters as $field => $value) {
            try {
                if (is_array($value)) {
                    $this->params->addBaseFilter(['terms' => [$field => $value]]);
                } else {
                    if (strpos($value, ',') !== false) {
                        $values = array_map('trim', explode(',', $value));
                        $this->params->addBaseFilter([
                            'terms_set' => [
                                $field => [
                                    'terms' => $values,
                                    'minimum_should_match_script' => [
                                        'source' => 'params.num_terms'
                                    ]
                                ]
                            ]
                        ]);
                    } else {
                        $this->params->addBaseFilter(['term' => [$field => $value]]);
                    }
                }
            } catch (\Exception $e) {
                error_log("Error parsing facet filter for field '$field' with value '$value': " . $e->getMessage());
            }
        }
    }
}