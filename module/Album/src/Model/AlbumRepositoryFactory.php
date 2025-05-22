<?php
namespace Album\Model;

use Album\Model\AlbumRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ObjectPropertyHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AlbumRepositoryFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        return new AlbumRepository(
            $container->get(AdapterInterface::class),
            new ObjectPropertyHydrator(),
            new Album()
        );
    }
}