<?php
namespace Apartment\Model;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Sql\Sql;

class ApartmentRepository implements ApartmentRepositoryInterface
{
    private $db;
    private $hydrator;
    private $apartment;

    const APARTMENT = 'apartment';

    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Apartment $apartmentPrototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->apartment = $apartmentPrototype;
    }

    public function findAll(): HydratingResultSet
    {
        $sql = new Sql($this->db);
        $select = $sql->select()
            ->columns(['id', 'name', 'city'])
            ->from(self::APARTMENT);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof \Laminas\Db\Adapter\Driver\ResultInterface || !$result->isQueryResult()) {
            throw new \RuntimeException('Failed retrieving apartments from the database');
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->apartment);
        $resultSet->initialize($result);
        return $resultSet;
    }

    public function find(int $id): ?Apartment
    {
        $sql = new Sql($this->db);
        $select = $sql->select()
            ->columns(['id', 'name', 'city'])
            ->from(self::APARTMENT)
            ->where(['id' => $id]);
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof \Laminas\Db\Adapter\Driver\ResultInterface || !$result->isQueryResult()) {
            throw new \RuntimeException('Failed retrieving apartment from the database');
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->apartment);
        $resultSet->initialize($result);
        return $resultSet->current();
    }

    public function insert(Apartment $apartment)
    {
        $sql = new Sql($this->db);
        $insert = $sql->insert(self::APARTMENT);
        $insert->values([
            'name' => $apartment->getName(),
            'city' => $apartment->getCity()
        ]);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        return $result->getGeneratedValue();
    }

    public function update(Apartment $apartment)
    {
        $sql = new Sql($this->db);
        $update = $sql->update(self::APARTMENT);
        $update->set([
            'name' => $apartment->getName(),
            'city' => $apartment->getCity()
        ])->where(['id' => (int) $apartment->getId()]);
        $statement = $sql->prepareStatementForSqlObject($update);
        return $statement->execute();
    }

    public function delete(int $id)
    {
        $sql = new Sql($this->db);
        $delete = $sql->delete(self::APARTMENT);
        $delete->where(['id' => $id]);
        $statement = $sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }
}