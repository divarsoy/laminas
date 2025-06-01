<?php
namespace Apartment;

use Apartment\Factory\ApartmentControllerFactory;
use Apartment\Model\ApartmentRepositoryFactory;
use Laminas\Router\Http\Segment;

return [
    'controllers' => [
        'factories' => [
            Controller\ApartmentController::class => ApartmentControllerFactory::class
        ],
    ],

    'router' => [
        'routes' => [
            'apartments' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/apartments[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApartmentController::class,
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'apartment' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'service_manager' => [
        'aliases' => [
            Model\ApartmentRepositoryInterface::class => Model\ApartmentRepository::class,
        ],
        'factories' => [
            Model\ApartmentRepository::class => ApartmentRepositoryFactory::class,
        ],
    ],
];