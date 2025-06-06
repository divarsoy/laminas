<?php
namespace PropertyTest\Controller;

use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Hydrator\ArraySerializableHydrator;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Property\Controller\PropertyController;
use Property\Model\PropertyRepository;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Property\Model\Property;
use Laminas\Http\Response;
class PropertyControllerTest extends AbstractHttpControllerTestCase
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
        $services->setService(PropertyRepository::class, $this->mockPropertyRepository());
        $services->setAllowOverride(false);
    }

    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    protected function mockPropertyRepository()
    {
        $this->repository = $this->createMock(PropertyRepository::class);
        return $this->repository;
    }

    public function testIndexReturnsResultsSuccessfully()
    {
        $resultset = new HydratingResultSet(
            new ArraySerializableHydrator(), 
            new Property(null, null, null, null, null, null)
        );

        $resultset->initialize([
            [
                'id' => 1,
                'name' => 'The Gate London City',
                'location_id' => '1',
                'emission' => "6.21kg CO2e",
                'rate' => 129,
                'imageurl' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwxfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
                'area' => 'Camden',
                'city' => 'London'
            ],
            [
                'id' => 2,
                'name' => 'The Chronicle Aparthotel',
                'location_id' => '2',
                'emission' => "8.54kg CO2e",
                'rate' => null,
                'imageurl' => 'https://images.unsplash.com/photo-1515263487990-61b07816b324?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3NTkwNjV8MHwxfHNlYXJjaHwyfHxhcGFydG1lbnR8ZW58MHx8fHwxNzQ4ODg2MTU3fDA&ixlib=rb-4.1.0&q=80&w=400',
                'area' => 'Aldgate',
                'city' => 'London'
            ]
        ]);

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($resultset);

        $this->dispatch('/api/properties', 'GET');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertModuleName('Property');
        $this->assertControllerName(PropertyController::class);
        $this->assertControllerClass('PropertyController');
        $this->assertMatchedRouteName('properties');
    }

}