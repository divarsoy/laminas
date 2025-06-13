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

    public function searchAll()
    {
        $stringFrom = '2025-06-01';
        $stringTo = '2025-06-04';
        $from = new Carbon($stringFrom);
        $to = new Carbon($stringTo);
        $availableRooms = $this->findAvailableRooms($from, $to);
        $apartments=[];
        foreach($availableRooms['aggregations']['available_rooms']['buckets'] as $bucket){
            $apartments[] = $bucket['key'];
        }
        return $this->findApartments($apartments, $from );
        
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

    private function findApartments(array $apartments, Carbon $date){

        $dateString = $date->toDateString();
        $params = [
            'index' => 'properties', // or 'rooms' if you're using a separate index
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [ "terms" => [ "apartment_id" => $apartments ]],
                            [ "term" => [ "date" => $dateString ]]
                        ],
                    ],
                ],
                '_source' => [
                    'apartment_id',
                    'data',
                    'rate',
                    'name',
                    'city',
                    'area',
                    'features',
                    'lat',
                    'long'
                ],
                'size' => 1000
            ]
        ];
        
        $results = $this->client->search($params);
        return $results;
    }

} 