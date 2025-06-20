<?php
namespace OpenSearchService\Command;

use Laminas\Cli\Command\AbstractParamAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use OpenSearchService\Model\Property;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use OpenSearch\ClientBuilder;

class IndexPropertiesCommand extends AbstractParamAwareCommand
{
    protected static $defaultName = 'property:index';
    protected const INDEX_NAME = 'properties';
    protected $openSearchService;
    protected $client;

    public function __construct($hosts)
    {
        parent::__construct();
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    
    }

    protected function configure()
    {
        $this->setDescription('Index property data from a JSON file into OpenSearch')
            ->addArgument('file', InputArgument::OPTIONAL, 'Path to the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createIndexIfDoesNotExist();

        if(!$input->hasArgument(1)){
            $this->generateDummyData(100000,50);
        }
        else {
            $filePath = $input->getArgument('file');
            if (!$filePath || !file_exists($filePath)) {
                $output->writeln("<error>File not found: $filePath</error>");
                return 1;
            }
            $json = file_get_contents($filePath);
            $data = json_decode($json, true);
            if (!$data) {
                $output->writeln("<error>Invalid JSON in file: $filePath</error>");
                return 1;
            }

            $index = 'properties';
            foreach ($data as $property) {
                $name = $property['name'] ?? null;
                $date = $property['date'] ?? null;
                if (!$name || !$date) {
                    $output->writeln("<error>Missing 'name' or 'date' field in data for document ID.</error>");
                    continue;
                }
                $id = $name . '_' . $date;
                $params = [
                    'index' => $index,
                    'id'    => $id,
                    'body'  => $property,
                ];
                $response = $this->client->index($params);
                $output->writeln("<info>Indexed property: $id</info>");
                $output->writeln(json_encode($response));
            }
        }
        return 0;
    }

    protected function createIndexIfDoesNotExist()
    {
        if (!$this->client->indices()->exists(['index' => self::INDEX_NAME])) {
            // Define your mapping and settings
            $params = [
                'index' => self::INDEX_NAME,
                'body'  => [
                    'mappings' => [
                        'properties' => [
                            'name' => ['type' => 'text'],
                            'date' => ['type' => 'date', 'format' => 'yyyy-MM-dd'],
                            'apartment_id' => ['type' => 'integer'],
                            'property_id' => ['type' => 'integer'],
                            'postcode' => ['type' => 'keyword'],
                            'city' => ['type' => 'keyword'],
                            'area' => ['type' => 'keyword'],
                            'apartment_type' => ['type' => 'keyword'],
                            'rate' => ['type' => 'float'],
                            'available' => ['type' => 'boolean'],
                            'rating' => ['type' => 'integer'],
                            'emission' => ['type' => 'float'],
                            'location' => ['type' => 'geo_point'],
                            'image_url' => ['type' => 'keyword'],
                            'apartment_facilities' => ['type' => 'keyword'],
                            'kitchen_facilities' => ['type' => 'keyword'],
                            'building_facilities' => ['type' => 'keyword'],
                            'building_type' => ['type' => 'keyword'],
                            'health_and_safety_facilities' => ['type' => 'keyword'],
                            'sustainability' => ['type' => 'keyword'],
                        ]
                    ]
                ]
            ];
        
            // Create index with mapping
            $this->client->indices()->create($params);
        }
    }

    protected function generateDummyData(int $totalProperties = 1000, int $bulkSize = 50) {
        $indexName = self::INDEX_NAME;
        $count = 0;
    
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 12, 31);
        $period = CarbonPeriod::create($startDate, $endDate);
    
        $bulkParams = ['body' => []];
    
        for ($propertyId = 1; $propertyId <= $totalProperties; $propertyId++) {
            $property = new Property();
            $baseRecord = $property->toArray();
            $baseRecord['property_id'] = $propertyId;
            $baseRecord['apartment_id'] = $propertyId;
    
            foreach ($period as $date) {
                $record = $baseRecord;
                $record['date'] = $date->format('Y-m-d');
    
                $recordId = sprintf('%d_%d_%s', $record['property_id'], $record['apartment_id'], $record['date']);
    
                $bulkParams['body'][] = [
                    'index' => [
                        '_index' => $indexName,
                        '_id' => $recordId,
                    ]
                ];
                $bulkParams['body'][] = $record;
    
                $count++;
    
                if ($count % $bulkSize === 0) {
                    try {
                        $response = $this->client->bulk($bulkParams);
                        if ($response['errors']) {
                            echo "Bulk insert errors occurred at record $count\n";
                            foreach ($response['items'] as $item) {
                                if (isset($item['index']['error'])) {
                                    $error = $item['index']['error'];
                                    $docId = $item['index']['_id'];
                                    echo "Error for document $docId:\n";
                                    echo "  Type: " . ($error['type'] ?? 'Unknown') . "\n";
                                    echo "  Reason: " . ($error['reason'] ?? 'Unknown') . "\n";
                                    if (isset($error['caused_by'])) {
                                        echo "  Caused by: " . $error['caused_by']['reason'] . "\n";
                                    }
                                    echo "\n";
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        echo "Error during bulk insert at record $count: " . $e->getMessage() . "\n";
                        echo "Stack trace: " . $e->getTraceAsString() . "\n";
                    }

                    $bulkParams = ['body' => []];
                    gc_collect_cycles();
                }
            }

            if ($propertyId % 100 === 0) {
                echo "Processed $propertyId properties, $count total records\n";
            }
        }
    
        if (!empty($bulkParams['body'])) {
            $response = $this->client->bulk($bulkParams);
            if ($response['errors']) {
                echo "Bulk insert errors occurred at final flush\n";
            }
        }
    
        echo "Completed indexing $count records for $totalProperties properties.\n";
    }
            
} 