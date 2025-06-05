<?php

declare(strict_types=1);

use ApartmentGenerator\Command\GenerateApartmentDataCommand;

return [
    'laminas-cli' => [
        'commands' => [
            'apartment:generate' => GenerateApartmentDataCommand::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            GenerateApartmentDataCommand::class => Laminas\ServiceManager\Factory\InvokableFactory::class,
        ],
    ],
];
