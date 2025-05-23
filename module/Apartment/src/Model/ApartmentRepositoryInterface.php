<?php
namespace Apartment\Model;
use Laminas\Db\ResultSet\HydratingResultSet;

interface ApartmentRepositoryInterface
{
    public function findAll(): HydratingResultSet;
    public function find(int $id): ?Apartment;
    public function insert(Apartment $apartment);
    public function update(Apartment $apartment);
    public function delete(int $id);

}