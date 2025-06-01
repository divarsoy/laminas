<?php
namespace ApartmentTest\model;

use Apartment\Model\ApartmentRepository;
use PHPUnit\Framework\TestCase;
use Laminas\Db\ResultSet\HydratingResultSet;
use Apartment\Model\Apartment;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Update;
use Laminas\Db\Sql\Delete;
use Laminas\Hydrator\ArraySerializableHydrator;

class ApartmentRepositoryTest extends TestCase
{
    private $hydrator;
    private $apartment;
    private $result;
    private $sql;
    private $statement;

    protected function setUp(): void
    {
        $this->hydrator = new ArraySerializableHydrator();
        $this->apartment = new Apartment(null, null, null);
        $this->result = $this->createMock(ResultInterface::class);
        $this->sql = $this->createMock(Sql::class);
        $this->statement = $this->createMock(StatementInterface::class);
    }

    public function testFindAll()
    {
        $this->result->expects($this->once())
            ->method('isQueryResult')
            ->willReturn(true);

        $select = $this->createMock(Select::class);
        $select->expects($this->once())
            ->method('columns')
            ->with(['id', 'name', 'city'])
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('from')
            ->with('apartment')
            ->willReturnSelf();

        $this->sql->expects($this->once())
            ->method('select')
            ->willReturn($select);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($select)
            ->willReturn($this->statement);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);
        $actual = $repository->findAll();
        
        $this->assertInstanceOf(HydratingResultSet::class, $actual);
        $this->assertSame($this->hydrator, $actual->getHydrator());
        $this->assertSame($this->apartment, $actual->getObjectPrototype());
    }

    public function testFindAllThrowsExceptionOnInvalidResult()
    {
        $this->result->expects($this->once())
            ->method('isQueryResult')
            ->willReturn(false);

        $select = $this->createMock(Select::class);
        $select->expects($this->once())
            ->method('columns')
            ->with(['id', 'name', 'city'])
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('from')
            ->with('apartment')
            ->willReturnSelf();

        $this->sql->expects($this->once())
            ->method('select')
            ->willReturn($select);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($select)
            ->willReturn($this->statement);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed retrieving apartments from the database');

        $repository->findAll();
    }

    public function testFindByIdReturnResultSuccessfully()
    {
        $id = 1;
        $expectedApartment = new Apartment('Test Apartment', 'London', $id);
        $resultData = ['id' => $id, 'name' => 'Test Apartment', 'city' => 'London'];

        $this->result->expects($this->once())
            ->method('isQueryResult')
            ->willReturn(true);

        $this->result->expects($this->once())
            ->method('current')
            ->willReturn($resultData);

        $select = $this->createMock(Select::class);

        $select->expects($this->once())
            ->method('columns')
            ->with(['id', 'name', 'city'])
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('from')
            ->with('apartment')
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('where')
            ->with(['id' => $id])
            ->willReturnSelf();

        $this->sql->expects($this->once())
            ->method('select')
            ->willReturn($select);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($select)
            ->willReturn($this->statement);

        $this->result->expects($this->once())
            ->method('current')
            ->willReturn($resultData);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);
        $actual = $repository->find($id);
        
        $this->assertInstanceOf(Apartment::class, $actual);
        $this->assertEquals($expectedApartment->getId(), $actual->getId());
        $this->assertEquals($expectedApartment->getName(), $actual->getName());
        $this->assertEquals($expectedApartment->getCity(), $actual->getCity());
    }

    public function testThrowsExceptionOnInvalidResult()
    {
        $id = 1;

        $this->result->expects($this->once())
            ->method('isQueryResult')
            ->willReturn(false);

        $select = $this->createMock(Select::class);
        $select->expects($this->once())
            ->method('columns')
            ->with(['id', 'name', 'city'])
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('from')
            ->with('apartment')
            ->willReturnSelf();

        $select->expects($this->once())
            ->method('where')
            ->with(['id' => $id])
            ->willReturnSelf();

        $this->sql->expects($this->once())
            ->method('select')
            ->willReturn($select);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($select)
            ->willReturn($this->statement);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed retrieving apartment from the database');

        $repository->find($id);
    }

    public function testInsertExecutesSuccessfully()
    {
        $id = 1;
        $apartment = new Apartment('Test Apartment', 'London');
        $insert = $this->createMock(Insert::class);
        $insert->expects($this->once())
            ->method('values')
            ->with([
                'name' => 'Test Apartment',
                'city' => 'London'
        ]);


        $this->sql->expects($this->once())
            ->method('insert')
            ->willReturn($insert);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($insert)
            ->willReturn($this->statement);

        $this->result->expects($this->once())
            ->method('getGeneratedValue')
            ->willReturn($id);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);
        $actual = $repository->insert($apartment);
        
        $this->assertEquals($id, $actual);
    }

    public function testUpdateExecutesSuccessfully()
    {
        $id = 1;
        $apartment = new Apartment('Test Apartment', 'London', $id);
        $update = $this->createMock(Update::class);
        $update->expects($this->once())
            ->method('set')
            ->with([
                'name' => 'Test Apartment',
                'city' => 'London'
            ])
            ->willReturnSelf();
        
        $update->expects($this->once())
            ->method('where')
            ->with(['id' => $id])
            ->willReturnSelf();

        $this->sql->expects($this->once())
            ->method('update')
            ->willReturn($update);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($update)
            ->willReturn($this->statement);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);
        $repository->update($apartment);       
    }

    public function testDeleteExecutesSuccessfully()
    {
        $id = 1;
        $delete = $this->createMock(Delete::class);
       
        $delete->expects($this->once())
            ->method('where')
            ->with(['id' => $id])
            ->willReturnSelf();

        $this->sql->expects($this->once())
            ->method('delete')
            ->willReturn($delete);

        $this->statement->expects($this->once())
            ->method('execute')
            ->willReturn($this->result);

        $this->sql->expects($this->once())
            ->method('prepareStatementForSqlObject')
            ->with($delete)
            ->willReturn($this->statement);

        $repository = new ApartmentRepository($this->sql, $this->hydrator, $this->apartment);
        $repository->delete($id);       
    }
}