<?php
namespace Property\Factory;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Property\Model\PropertyRepositoryInterface;
use Property\Controller\PropertyController;

class PropertyControllerFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        return new PropertyController(
            $container->get(PropertyRepositoryInterface::class)
        );
    }
}