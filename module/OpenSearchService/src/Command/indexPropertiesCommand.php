<?php
namespace OpenSearchService\Command;

use OpenSearchService\Service\OpenSearchService;
use Laminas\Cli\Command\AbstractParamAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class IndexPropertiesCommand extends AbstractParamAwareCommand
{
    protected static $defaultName = 'property:index';

    private $openSearchService;

    public function __construct(OpenSearchService $openSearchService)
    {
        parent::__construct();
        $this->openSearchService = $openSearchService;
    }

    protected function configure()
    {
        $this->setDescription('Index property data from a JSON file into OpenSearch')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
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
        $client = $this->openSearchService->getClient();
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
            $response = $client->index($params);
            $output->writeln("<info>Indexed property: $id</info>");
            $output->writeln(json_encode($response));
        }
        return 0;
    }
} 