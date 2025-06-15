<?php
namespace OpenSearchService\Service;

use OpenSearch\ClientBuilder;
use Carbon\Carbon;

class OpenSearchService
{
    /** @var \OpenSearch\Client */
    private $client;
    private $index = 'properties';

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
        $ignoreAvailability = false;

        $must = [];
        $filter = [];

        // Full-text search
        if ($searchText) {
            $must[] = [
                'multi_match' => [
                    'query' => $searchText,
                    'fields' => ['name^2', 'description']
                ]
            ];
        }

        // Rate range filter
        if ($rateMin !== null || $rateMax !== null) {
            $range = [];
            if ($rateMin !== null) {
                $range['gte'] = (float)$rateMin;
            }
            if ($rateMax !== null) {
                $range['lte'] = (float)$rateMax;
            }
            $filter[] = ['range' => ['rate' => $range]];
        }

        // Date range filter 
        try {
            if ($startDate === null) {
                $ignoreAvailability = true;
                $from = Carbon::now();
            } else {
                $from = new Carbon($startDate);
                if (!$from->isValid()) {
                    throw new \InvalidArgumentException('Invalid start date format. Expected format: YYYY-MM-DD');
                }
            }

            if ($endDate !== null) {
                $to = new Carbon($endDate);
                if (!$to->isValid()) {
                    throw new \InvalidArgumentException('Invalid end date format. Expected format: YYYY-MM-DD');
                }

                if ($to->lt($from)) {
                    throw new \InvalidArgumentException('End date must be after start date');
                }

                $numberOfDays = $from->diffInDays($to);
                if ($numberOfDays < 1) {
                    throw new \InvalidArgumentException('Date range must be at least 1 day');
                }

                $dateRange = [
                    'gte' => $from->toDateString(),
                    'lt' => $to->toDateString()
                ];
                $filter[] = ['range' => ['date' => $dateRange]];

                // Add availability filter only if we're not ignoring it
                if (!$ignoreAvailability) {
                    $filter[] = [
                        'bool' => [
                            'must' => [
                                [
                                    'term' => ['available' => true]
                                ],
                                [
                                    'range' => [
                                        'date' => [
                                            'gte' => $from->toDateString(),
                                            'lt' => $to->toDateString()
                                        ]
                                    ]
                                ]
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

        // Facet filters
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                $filter[] = ['terms' => [$field => $value]];
            } else {
                $filter[] = ['term' => [$field => $value]];
            }
        }

        $aggs = [
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
                            'script' => "params.dateCount == $numberOfDays"
                        ]
                    ]
                ],                        
            ],
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
            'index' => 'properties',
            'body' => [
                'size' => 0,
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter
                    ]
                ],
                'aggs' => $aggs
            ]
        ];

        $availableRooms = $this->client->search($params);
        $apartments = [];
        if (isset($availableRooms['aggregations']['available_rooms']['buckets'])) {
            foreach($availableRooms['aggregations']['available_rooms']['buckets'] as $bucket) {
                $apartments[] = $bucket['key'];
            }
        }

        if (empty($apartments)) {
            return [
                'results' => [],
                'facets' => [],
                'total' => 0
            ];
        }

        return $this->findApartments($params, $apartments, $from);
    }

    private function findAvailableRooms(Carbon $from, Carbon $to){
        $numberOfDays = $from->diffInDays($to);

        $params = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'range' => [
                                    'date' => [
                                        'gte' => $from->toDateString(),
                                        'lt' => $to->toDateString()
                                    ]
                                ]
                            ],
                            [
                                'term' => [
                                    'available' => 1
                                ]
                            ]
                        ]
                    ]
                ],
                'aggs' => [
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
                                    'script' => "params.dateCount == $numberOfDays"
                                ]
                            ]
                        ],                        
                    ]
                ],
                'size' => 0
            ]
        ];
        return $this->client->search($params);
    }

    private function findApartments(array $params, array $apartments, Carbon $date) {
        $dateString = $date->toDateString();
        $params['body']['size'] = 1000;
        $params['body']['query']['bool']['filter'][] = [ "terms" => [ "apartment_id" => $apartments ]];
        $params['body']['query']['bool']['filter'][] = [ "term" => [ "date" => $dateString ]];   
        
        $results = $this->client->search($params);
        
        return [
            'results' => $results['hits']['hits'] ?? [],
            'facets' => $results['aggregations'] ?? [],
            'total' => $results['hits']['total']['value'] ?? 0
        ];
    }

} 