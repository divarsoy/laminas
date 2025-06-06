<?php
namespace PropertyTest\Model;

use PHPUnit\Framework\TestCase;
use Laminas\Hydrator\ArraySerializableHydrator;
use Property\Model\Property;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Property\Model\PropertyRepository;
use Laminas\Db\ResultSet\HydratingResultSet;

class PropertyRepositoryTest extends TestCase
{
    public function testFindAll()
    {

        $hydrator = new ArraySerializableHydrator();
        $apartment = new Property(null, null, null, null, null, null, null, null);
        $result = $this->createMock(ResultInterface::class);
        $sql = $this->createMock(Sql::class);
        $statement = $this->createMock(StatementInterface::class);

        $result->expects($this->once())
        ->method('isQueryResult')
        ->willReturn(true);

        $select = $this->createMock(Select::class);
        $select->expects($this->once())
            ->method('columns')
            ->with(['id', 'name', 'location_id', 'emission', 'rate', 'imageurl'])
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('from')
            ->with('property')
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('join')
            ->with('location','location_id=location.id',['area', 'city'])
            ->willReturnSelf();

        $sql->expects($this->once())
            ->method('select')
            ->willReturn($select);

        $statement->expects($this->once())
            ->method('execute')
            ->willReturn($result);

        $sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($select)
            ->willReturn($statement);

        $repository = new PropertyRepository($sql, $hydrator, $apartment);
        $actual = $repository->findAll();
        
        $this->assertInstanceOf(HydratingResultSet::class, $actual);
        $this->assertSame($hydrator, $actual->getHydrator());
        $this->assertSame($apartment, $actual->getObjectPrototype());
    }

}