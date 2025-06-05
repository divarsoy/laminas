<?php
namespace Property\Controller;
use Property\Model\PropertyRepositoryInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

Class PropertyController extends AbstractActionController
{
    private $repository;

    public function __construct(PropertyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function indexAction()
    {
        $resultSet = $this->repository->findAll();
        return new JsonModel($resultSet->toArray());
    }
}
