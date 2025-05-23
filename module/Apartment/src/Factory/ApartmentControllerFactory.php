<?php
namespace Apartment\Factory;

use Apartment\Controller\ApartmentController;
use Apartment\Model\ApartmentRepositoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;


class ApartmentControllerFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        return new ApartmentController(
            $container->get(ApartmentRepositoryInterface::class)
        );
    }
}