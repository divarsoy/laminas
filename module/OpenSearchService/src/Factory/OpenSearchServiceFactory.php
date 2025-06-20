<?php
namespace OpenSearchService\Factory;
use Laminas\ServiceManager\Factory\FactoryInterface;
use OpenSearchService\Service\OpenSearchService;
use OpenSearchService\Model\OpenSearchParams;

class OpenSearchServiceFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        $config = $container->get('config');
        $hosts = $config['opensearch']['client']['hosts'] ?? ['opensearch:9200'];
        $index = $config['opensearch']['client']['index'] ?? 'properties';
        $params = new OpenSearchParams($index);
        return new OpenSearchService($params, $hosts);
    }
}