<?php
namespace Apartment;

use Property\Factory\PropertyControllerFactory;
use Property\Factory\PropertyRepositoryFactory;
use Property\Controller\PropertyController;
use Property\Model\PropertyRepositoryInterface;
use Property\Model\PropertyRepository;
use Laminas\Router\Http\Literal;

return [
    'controllers' => [
        'factories' => [
            PropertyController::class => PropertyControllerFactory::class
        ],
    ],

    'router' => [
        'routes' => [
            'properties' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/api/properties',
                    'defaults' => [
                        'controller' => PropertyController::class,
                        'action' => 'index'
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'property' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'service_manager' => [
        'aliases' => [
            PropertyRepositoryInterface::class => PropertyRepository::class,
        ],
        'factories' => [
            PropertyRepository::class => PropertyRepositoryFactory::class,
        ],
    ],
];