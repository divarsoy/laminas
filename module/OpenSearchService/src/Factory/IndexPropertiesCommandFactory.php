<?php
namespace OpenSearchService\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use OpenSearchService\Service\OpenSearchService;
use OpenSearchService\Command\IndexPropertiesCommand;

class IndexPropertiesCommandFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        $config = $container->get('config');
        $hosts = $config['opensearch']['client']['hosts'];
        $openSearchService = new OpenSearchService($hosts);
        return new IndexPropertiesCommand($openSearchService);
    }
}