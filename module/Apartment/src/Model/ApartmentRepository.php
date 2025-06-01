<?php
namespace Apartment\Model;

use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Sql\Sql;

class ApartmentRepository implements ApartmentRepositoryInterface
{
    private $sql;
    private $hydrator;
    private $apartment;

    const APARTMENT = 'apartment';

    public function __construct(
        Sql $sql,
        HydratorInterface $hydrator,
        Apartment $apartmentPrototype
    )
    {
        $this->sql = $sql;
        $this->hydrator = $hydrator;
        $this->apartment = $apartmentPrototype;
    }

    public function findAll(): HydratingResultSet
    {
        $select = $this->sql->select()
            ->columns(['id', 'name', 'city'])
            ->from(self::APARTMENT);
        $statement = $this->sql->prepareStatementForSqlObject($select);
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
        $select = $this->sql->select()
            ->columns(['id', 'name', 'city'])
            ->from(self::APARTMENT)
            ->where(['id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
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
        $insert = $this->sql->insert(self::APARTMENT);
        $insert->values([
            'name' => $apartment->getName(),
            'city' => $apartment->getCity()
        ]);
        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        return $result->getGeneratedValue();
    }

    public function update(Apartment $apartment)
    {
        $update = $this->sql->update(self::APARTMENT);
        $update->set([
            'name' => $apartment->getName(),
            'city' => $apartment->getCity()
        ])->where(['id' => (int) $apartment->getId()]);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        return $statement->execute();
    }

    public function delete(int $id)
    {
        $delete = $this->sql->delete(self::APARTMENT);
        $delete->where(['id' => $id]);
        $statement = $this->sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }
}