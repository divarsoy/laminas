<?php
namespace Album;

use Album\Model\AlbumRepositoryFactory;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Album\Model\AlbumTableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\AlbumController::class => ReflectionBasedAbstractFactory::class,
            Controller\AlbumApiController::class => ReflectionBasedAbstractFactory::class,
        ],
    ],

    'router' => [
        'routes' => [
            'album' => [
                'type'  => Segment::class,
                'options' => [
                    'route' => '/album[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AlbumController::class,
                        'action' => 'index',
                    ],
                ]
            ],
            'albumApi' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/api/v1/album[/:id]',
                    'constraints' => [
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AlbumApiController::class,
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'get' => [
                        'type' => \Laminas\Router\Http\Method::class,
                        'options' => [
                            'verb' => 'get',
                            'defaults' => [
                                'action' => 'getList',
                            ],
                        ],
                    ],
                    'get-id' => [
                        'type' => \Laminas\Router\Http\Method::class,
                        'options' => [
                            'verb' => 'get',
                            'defaults' => [
                                'action' => 'get',
                            ],
                        ],
                    ],
                    'post' => [
                        'type' => \Laminas\Router\Http\Method::class,
                        'options' => [
                            'verb' => 'post',
                            'defaults' => [
                                'action' => 'create',
                            ],
                        ],
                    ],
                    'put' => [
                        'type' => \Laminas\Router\Http\Method::class,
                        'options' => [
                            'verb' => 'put',
                            'defaults' => [
                                'action' => 'update',
                            ],
                        ],
                    ],
                    'delete' => [
                        'type' => \Laminas\Router\Http\Method::class,
                        'options' => [
                            'verb' => 'delete',
                            'defaults' => [
                                'action' => 'delete',
                            ],
                        ],
                    ],
                ],
            ],
        ]
    ],

    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],

    'service_manager' => [
        'aliases' => [
            Model\AlbumRepositoryInterface::class => Model\AlbumRepository::class,
        ],
        'factories' => [
            Model\AlbumTable::class => AlbumTableFactory::class,
            Model\AlbumRepository::class => AlbumRepositoryFactory::class,
        ],
    ],
];