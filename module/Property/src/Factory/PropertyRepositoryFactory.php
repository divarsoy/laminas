<?php
namespace Property\Factory;

use Property\Model\PropertyRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Property\Model\Property;

class PropertyRepositoryFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        return new PropertyRepository(
            new Sql($container->get(AdapterInterface::class)),
            new ArraySerializableHydrator,
            new Property(null, null, null, null, null, null)
        );
    }
}