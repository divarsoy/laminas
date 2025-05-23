<?php
namespace Apartment\Controller;

use Apartment\Model\ApartmentRepositoryInterface;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use Apartment\Model\ApartmentRepository;
use Apartment\Model\Apartment;
use Apartment\Model\ApartmentInputFilter;
use Laminas\Http\Response;

Class ApartmentController extends AbstractRestfulController
{
    private ApartmentRepository $repository;

    public function __construct(ApartmentRepositoryInterface $apartmentRepository)
    {
        $this->repository = $apartmentRepository;
    }

    public function getList()
    {
        $rowset = $this->repository->findAll();
        return new JsonModel($rowset->toArray());
    }

    public function get($id)
    {
        $apartment = $this->repository->find((int) $id);
        if(!$apartment){
            return $this->notFoundAction();
        }
        return new JsonModel($apartment->toArray());
    }

    public function create($data)
    {
        $filter = new ApartmentInputFilter();
        $filter->setData($data);

        if(! $filter->isValid()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_422);
            return new JsonModel([
                'errors' => $filter->getMessages()
            ]);
        }

        $validatedData = $filter->getValues();
        $apartment = new Apartment($validatedData['name'], $validatedData['city']);
        $id = $this->repository->insert($apartment);
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_201);
        return new JsonModel([
            'id' => $id,
            'name' => $apartment->getName(),
            'city' => $apartment->getCity()
        ]);
    }

    public function update($id, $data)
    {
        $filter = new ApartmentInputFilter();
        $filter->setData($data);

        if(! $filter->isValid()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_422);
            return new JsonModel([
                'errors' => $filter->getMessages()
            ]);
        }

        $validatedData = $filter->getValues();
        $apartment = $this->repository->find($id);
        if(!$apartment) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return new JsonModel(['error' => 'Apartment not found']);
        }
        $updatedApartment = new Apartment($validatedData['name'], $validatedData['city'], $apartment->getId());

        $this->repository->update($updatedApartment);

        return new JsonModel(['status' => 'updated']);
        
    }

    public function delete($id)
    {
        $this->repository->delete($id);
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_204);
        return $this->getResponse();
    }


}