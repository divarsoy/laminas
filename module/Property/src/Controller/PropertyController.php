<?php
namespace Property\Controller;
use Property\Model\PropertyRepositoryInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use OpenSearchService\Service\OpenSearchService;

Class PropertyController extends AbstractActionController
{
    private $repository;
    private $openSearchService;

    public function __construct(PropertyRepositoryInterface $repository, OpenSearchService $openSearchService)
    {
        $this->repository = $repository;
        $this->openSearchService = $openSearchService;
    }

    public function indexAction()
    {
        $resultSet = $this->repository->findAll();
        return new JsonModel($resultSet->toArray());
    }

    public function searchAction()
    {
        $queryParams = $this->params()->fromQuery();
        $response = $this->openSearchService->search($queryParams);
        return new JsonModel([
            'results' => $response['results'],
            'facets' => $response['facets'],
            'total' => $response['total']
        ]);
    }
}
