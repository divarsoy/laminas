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

        $this->dispatch('/api/apartments', 'GET');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertModuleName('Apartment');
        $this->assertControllerName(ApartmentController::class);
        $this->assertControllerClass('ApartmentController');
        $this->assertMatchedRouteName('apartments');
    }

    public function testGeActionCanBeAccessedSuccessfully()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Apartment(1, 'Test Apartment 1', 'London'));

            $this->dispatch('/api/apartments/1', 'GET');
            $this->assertResponseStatusCode(Response::STATUS_CODE_200);
            $this->assertModuleName('Apartment');
            $this->assertControllerName(ApartmentController::class);
            $this->assertMatchedRouteName('apartments');
    }
    public function testGetActionWillReturnNotFoundWhenApartmentDoesNotExist()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->dispatch('/api/apartments/1', 'GET');
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }

    public function testCreateActionWillReturnApartmentOnSuccess()
    {
        $this->repository->expects($this->once())
            ->method('insert')
            ->with($this->isInstanceOf(Apartment::class))
            ->willReturn(1);

        $this->dispatch('/api/apartments', 'POST', [
            'name' => 'Test Apartment 1',
            'city' => 'London'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_201);
        $response = $this->getResponse()->getContent();
        $this->assertSame(json_encode([
            'id' => 1,
            'name' => 'Test Apartment 1',
            'city' => 'London'
        ]), $response);
        $this->assertModuleName('Apartment');
        $this->assertControllerName(ApartmentController::class);
        $this->assertControllerClass('ApartmentController');
        $this->assertMatchedRouteName('apartments');
    }


    public function testCreateActionWillReturnValidationErrorIfNameIsMissing()
    {
        $this->dispatch('/api/apartments', 'POST', [
            'city' => 'London'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getResponse()->getContent();
        $this->assertSame('{"errors":{"name":{"isEmpty":"Value is required and can\u0027t be empty"}}}', $response);
    }

    public function testCreateActionWillReturnValidationErrorIfCityIsMissing()
    {
        $this->dispatch('/api/apartments', 'POST', [
            'name' => 'Test Apartment 1'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getResponse()->getContent();
        $this->assertSame('{"errors":{"city":{"isEmpty":"Value is required and can\u0027t be empty"}}}', $response);
    }

    public function testCreateActionWillReturnValidationErrorIfNameIsLongerThan255Characters()
    {
        $longString = 'Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name';
        $this->dispatch('/api/apartments', 'POST', [
            'name' => $longString,
            'city' => 'London'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getREsponse()->getContent();
        $this->assertSame(json_encode([
            'errors' => [
                'name' => [
                    'stringLengthTooLong' => 'The input is more than 255 characters long'
                ]
            ]
        ]), $response);
    }

    public function testCreateActionWillReturnValidationErrorIfCityIsLongerThan100Characters()
    {
        $longString = 'City with more than 100 characters, City with more than 100 characters, City with more than 100 chara';
        $this->dispatch('/api/apartments', 'POST', [
            'name' => 'Test Apartment 1',
            'city' => $longString
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getREsponse()->getContent();
        $this->assertSame(json_encode([
            'errors' => [
                'city' => [
                    'stringLengthTooLong' => 'The input is more than 100 characters long'
                ]
            ]
        ]), $response);
    }


    public function testUpdateActionWillUpdateOnSuccess()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Apartment('London', 'Test Apartment 1', 1));

        $this->repository->expects($this->once())
            ->method('update')
            ->with($this->callback(function($apartment) {
                return $apartment instanceof Apartment 
                    && (int) $apartment->getId() === 1
                    && $apartment->getName() === 'Test Apartment 2'
                    && $apartment->getCity() === 'London';
            }));

        $this->dispatch('/api/apartments/1', 'PUT', [
            'name' => 'Test Apartment 2', 
            'city' => 'London' 
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $response = $this->getResponse()->getContent();
        $this->assertSame(json_encode(['status' => 'updated']), $response);
    }

    public function testUpdateActionWillReturnValidationErrorIfNameIsMissing()
    {
        $this->dispatch('/api/apartments/1', 'PUT', [
            'city' => 'London'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getResponse()->getContent();
        $this->assertSame('{"errors":{"name":{"isEmpty":"Value is required and can\u0027t be empty"}}}', $response);
    }

    public function testUpdateActionWillReturnValidationErrorIfCityIsMissing()
    {
        $this->dispatch('/api/apartments/1', 'PUT', [
            'name' => 'Test Apartment 1'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getResponse()->getContent();
        $this->assertSame('{"errors":{"city":{"isEmpty":"Value is required and can\u0027t be empty"}}}', $response);
    }

    public function testUpdateActionWillReturnValidationErrorIfNameIsLongerThan255Characters()
    {
        $longString = 'Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name with more than 255 characters, Name';
        $this->dispatch('/api/apartments/1', 'PUT', [
            'name' => $longString,
            'city' => 'London'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getREsponse()->getContent();
        $this->assertSame(json_encode([
            'errors' => [
                'name' => [
                    'stringLengthTooLong' => 'The input is more than 255 characters long'
                ]
            ]
        ]), $response);
    }

    public function testUpdateActionWillReturnValidationErrorIfCityIsLongerThan100Characters()
    {
        $longString = 'City with more than 100 characters, City with more than 100 characters, City with more than 100 chara';
        $this->dispatch('/api/apartments/1', 'PUT', [
            'name' => 'Test Apartment 1',
            'city' => $longString
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_422);
        $response = $this->getREsponse()->getContent();
        $this->assertSame(json_encode([
            'errors' => [
                'city' => [
                    'stringLengthTooLong' => 'The input is more than 100 characters long'
                ]
            ]
        ]), $response);
    }

    public function testUpdateActionWillReturnNotFoundWhenApartmentDoesNotExist()
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $this->dispatch('/api/apartments/1', 'PUT', [
            'name' => 'Test Apartment 1',
            'city' => 'London'
        ]);
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }

    public function testDeleteActionWillReturn204Successfully()
    {
        $this->repository->expects($this->once())
            ->method('delete')
            ->with(1);
        $this->dispatch('/api/apartments/1', 'DELETE');
        $this->assertResponseStatusCode(Response::STATUS_CODE_204);
    }
}