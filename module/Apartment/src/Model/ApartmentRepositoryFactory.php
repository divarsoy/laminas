<?php
namespace Apartment\Model;

use Apartment\Model\ApartmentRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ApartmentRepositoryFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        return new ApartmentRepository(
            new Sql($container->get(AdapterInterface::class)),
            new ArraySerializableHydrator,
            new Apartment(null, null, null)
        );
    }
}