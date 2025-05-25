<?php
namespace ApartmentTest\controller;

use Apartment\Controller\ApartmentController;
use Apartment\Model\ApartmentRepository;
use Apartment\Model\Apartment;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Laminas\Stdlib\ArrayUtils;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Http\Response;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ArraySerializableHydrator;

class ApartmentControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $repository;

    protected function setup():void
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setup();
        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    protected function configureServiceManager(ServiceManager $services):void
    {
        $services->setAllowOverride(true);
        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(ApartmentRepository::class, $this->mockApartmentRepository());
        $services->setAllowOverride(false);        
    }

    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    protected function mockApartmentRepository()
    {
        $this->repository = $this->createMock(ApartmentRepository::class);
        return $this->repository;
    }

    public function testListCanBeAccessed()
    {
        $resultSet = new HydratingResultSet(
            new ArraySerializableHydrator(),
            new Apartment(null, null)
        );
        
        // Initialize the result set with test data
        $resultSet->initialize([
            [
                'id' => 1,
                'name' => 'Test Apartment 1',
                'city' => 'Test City 1'
            ],
            [
                'id' => 2,
                'name' => 'Test Apartment 2',
                'city' => 'Test City 2'
            ]
        ]);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($resultSet);

        $this->dispatch('/apartments', 'GET');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertModuleName('Apartment');
        $this->assertControllerName(ApartmentController::class);
        $this->assertControllerClass('ApartmentController');
        $this->assertMatchedRouteName('apartments');
    }
}