<?php
namespace OpenSearchService\Service;

use OpenSearch\ClientBuilder;
use Carbon\Carbon;

class OpenSearchService
{
    /** @var \OpenSearch\Client */
    private $client;
    private $index = 'properties';
    private $filter = [];
    private $must = [];
    private $ignoreAvailability = false;
    private ?Carbon $startDate;
    private ?Carbon $endDate;
    private int $numberOfDays = 1;

    public function __construct(array $hosts = ['opensearch:9200'])
    {
        $this->client = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }

    public function getClient(): \OpenSearch\Client
    {
        return $this->client;
    }

    public function search($queryParams)
    {
        $searchText = $queryParams['q'] ?? null;
        $filters = $queryParams['filters'] ?? [];
        $rateMin = $queryParams['rate_min'] ?? null;
        $rateMax = $queryParams['rate_max'] ?? null;
        $startDate = $queryParams['start_date'] ?? null;
        $endDate = $queryParams['end_date'] ?? null;

        $this->reset();
        $this->parseFacetFilters($filters);
        $this->parseDateFilter($startDate,$endDate);
        $this->parseSearchString($searchText);
        $this->parseRateRange($rateMin, $rateMax);
        $this->ignoreAvailabilityCheck();
        $params = $this->buildParams();

        if($this->ignoreAvailability){
            $results = $this->client->search($params);
            return [
                'results' => $results['hits']['hits'] ?? [],
                'facets' => $results['aggregations'] ?? [],
                'total' => $results['hits']['total']['value'] ?? 0
            ];
        }
        
        $availableApartments = $this->findAvailableApartments($params);
        if (empty($availableApartments)) {
            return [
                'results' => [],
                'facets' => [],
                'total' => 0
            ];
        }
        $results = $this->findApartmentsForDate($params, $availableApartments, $this->startDate);

        return [
            'results' => $results['hits']['hits'] ?? [],
            'facets' => $results['aggregations'] ?? [],
            'total' => $results['hits']['total']['value'] ?? 0
        ];
    }

    private function findAvailableApartments($params)
    {
        $params['body']['aggs']['available_rooms'] = [
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
                        'script' => "params.dateCount == $this->numberOfDays"
                    ]
                ]
            ],                
        ];

        $availableApartments = $this->client->search($params);
        $apartments = [];
        if (isset($availableApartments['aggregations']['available_rooms']['buckets'])) {
            foreach($availableApartments['aggregations']['available_rooms']['buckets'] as $bucket) {
                $apartments[] = $bucket['key'];
            }
        }
        return $apartments;
    }
    private function buildParams()
    {
        $aggs = [
            'apartment_type' => ['terms' => ['field' => 'apartment_type']],
            'cities' => ['terms' => ['field' => 'city']],
            'building_type' => ['terms' => ['field' => 'building_type']],
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

        $params = [
            'index' => $this->index,
            'body' => [
                'size' => 20,
                'query' => [
                    'bool' => [
                        'must' => $this->must,
                        'filter' => $this->filter
                    ]
                ],
                'aggs' => $aggs
            ]
        ];
        return $params;
    }
    private function ignoreAvailabilityCheck()
    {
        if ($this->ignoreAvailability) {
            $this->must[] = [
                'term' => ['date' => $this->startDate->toDateString()]
            ];  
        }
    }

    private function parseRateRange($rateMin, $rateMax)
    {
        if ($rateMin !== null || $rateMax !== null) {
            $range = [];
            if ($rateMin !== null) {
                $range['gte'] = (float)$rateMin;
            }
            if ($rateMax !== null) {
                $range['lte'] = (float)$rateMax;
            }
            $this->filter[] = ['range' => ['rate' => $range]];
        }
    }

    private function parseSearchString($searchText)
    {
        if ($searchText) {
            $this->must[] = [
                'multi_match' => [
                    'query' => $searchText,
                    'fields' => ['name^2', 'description']
                ]
            ];
        }
    }

    private function parseDateFilter($start, $end)
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

            if( $end == null){
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

                $dateRange = [
                    'gte' => $this->startDate->toDateString(),
                    'lt' => $this->endDate->toDateString()
                ];
                $this->filter[] = ['range' => ['date' => $dateRange]];

                if (!$this->ignoreAvailability) {
                    
                    $this->must[] = [
                            'term' => ['available' => true]
                    ];
                    $this->must[] = [
                            'range' => [
                                'date' => [
                                    'gte' => $this->startDate->toDateString(),
                                    'lt' => $this->endDate->toDateString()
                                ]
                            ]
                    ];

                }
            }
        } catch (\Exception $e) {
            // Log the error and rethrow
            error_log("Date validation error: " . $e->getMessage());
            throw $e;
        }
    }
    private function parseFacetFilters($filters)
    {
        // Facet filters
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $this->filter[] = ['terms' => [$field => $value]];
            } else {
                $this->filter[] = ['term' => [$field => $value]];
            }
        }
    }

    private function reset()
    {
        $this->filter = [];
        $this->must = [];
        $this->ignoreAvailability = false;
        $this->startDate = null;
        $this->endDate = null;
        $this->numberOfDays = 1;
    }

    private function findApartmentsForDate(array $params, array $apartments, Carbon $date) {
        $dateString = $date->toDateString();
        $params['body']['size'] = 20;
        $params['body']['query']['bool']['filter'][] = [ "terms" => [ "apartment_id" => $apartments ]];
        $params['body']['query']['bool']['filter'][] = [ "term" => [ "date" => $dateString ]];

        return $this->client->search($params);
    }

} 