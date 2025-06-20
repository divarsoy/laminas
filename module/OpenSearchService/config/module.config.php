<?php
namespace OpenSearchService;
use OpenSearchService\Command\IndexPropertiesCommand;
use OpenSearchService\Factory\IndexPropertiesCommandFactory;
use OpenSearchService\Service\OpenSearchService;
use OpenSearchService\Factory\OpenSearchServiceFactory;

return [
    'service_manager' => [
        'factories' => [
            OpenSearchService::class => OpenSearchServiceFactory::class,
            IndexPropertiesCommand::class => IndexPropertiesCommandFactory::class
        ],
    ],
    'opensearch' => [
        'client' => [
            'hosts' => [
                'http://opensearch:9200'
            ],
            'index' => 'properties',
        ],
    ],
    'laminas-cli' => [
        'commands' => [
            'property:index' => IndexPropertiesCommand::class,
        ],
    ],
];
