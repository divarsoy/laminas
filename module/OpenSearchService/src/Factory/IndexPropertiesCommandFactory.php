<?php
namespace OpenSearchService\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use OpenSearchService\Service\OpenSearchService;
use OpenSearchService\Command\IndexPropertiesCommand;
use OpenSearchService\Model\OpenSearchParams;
use OpenSearch\Client;

class IndexPropertiesCommandFactory implements FactoryInterface
{
    public function __invoke(\Psr\Container\ContainerInterface $container, $requestedName, array|null $options = null)
    {
        $config = $container->get('config');
        $hosts = $config['opensearch']['client']['hosts'] ?? ['opensearch:9200'];
        return new IndexPropertiesCommand($hosts);
    }
}