<?php
namespace Property\Model;

use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Sql\Sql;

class PropertyRepository implements PropertyRepositoryInterface
{
    private $sql;
    private $hydrator;
    private $property;

    const PROPERTY= 'property';

    public function __construct(
        Sql $sql,
        HydratorInterface $hydrator,
        Property $propertyPrototype
    )
    {
        $this->sql = $sql;
        $this->hydrator = $hydrator;
        $this->property = $propertyPrototype;
    }

    public function findAll(): HydratingResultSet
    {
        $select = $this->sql->select()
            ->columns(['id', 'name', 'location_id', 'emission', 'rate', 'imageurl'])
            ->from(self::PROPERTY);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof \Laminas\Db\Adapter\Driver\ResultInterface || !$result->isQueryResult()) {
            throw new \RuntimeException('Failed retrieving properties from the database');
        }

        // var_dump($result->current());
        $resultSet = new HydratingResultSet($this->hydrator, $this->property);
        $resultSet->initialize($result);
        return $resultSet;
    }
}